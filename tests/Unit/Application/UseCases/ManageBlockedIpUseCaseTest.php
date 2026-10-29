<?php

declare(strict_types=1);

namespace Tests\Unit\Application\UseCases;

use App\Application\UseCases\ManageBlockedIpUseCase;
use App\Infrastructure\Persistence\Eloquent\Models\AuditLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class ManageBlockedIpUseCaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_audit_failure_rolls_back_ip_block(): void
    {
        $this->seed();
        $log = AuditLog::query()->create([
            'action' => 'Gagal Login',
            'model_type' => 'user',
            'ip_address' => '8.8.8.8',
        ]);

        AuditLog::creating(function (): void {
            throw new RuntimeException('Audit tidak tersedia.');
        });

        $this->expectException(RuntimeException::class);

        try {
            app(ManageBlockedIpUseCase::class)->block($log, 1, '9.9.9.9');
        } finally {
            $this->assertDatabaseCount('blocked_ips', 0);
        }
    }
}
