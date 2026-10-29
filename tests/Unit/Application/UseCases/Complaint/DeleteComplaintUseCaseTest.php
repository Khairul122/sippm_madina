<?php

declare(strict_types=1);

namespace Tests\Unit\Application\UseCases\Complaint;

use App\Application\UseCases\Complaint\DeleteComplaintUseCase;
use App\Infrastructure\Persistence\Eloquent\Models\AuditLog;
use App\Infrastructure\Persistence\Eloquent\Models\Complaint;
use App\Infrastructure\Persistence\Eloquent\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class DeleteComplaintUseCaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_audit_failure_rolls_back_soft_delete(): void
    {
        $this->seed();
        $user = User::query()->where('email', 'masyarakat@gmail.com')->firstOrFail();
        $complaint = Complaint::query()->create([
            'ticket_number' => 'PGD-2026-999999',
            'user_id' => $user->id,
            'title' => 'Laporan uji',
            'description' => 'Deskripsi uji.',
            'category' => 'Infrastruktur',
            'target_type' => 'opd',
            'status' => 'diajukan',
        ]);

        AuditLog::creating(function (): void {
            throw new RuntimeException('Audit tidak tersedia.');
        });

        $this->expectException(RuntimeException::class);

        try {
            app(DeleteComplaintUseCase::class)->execute($complaint->id, $user->id);
        } finally {
            $this->assertDatabaseHas('complaints', ['id' => $complaint->id, 'deleted_at' => null]);
        }
    }
}
