<?php

declare(strict_types=1);

namespace Tests\Feature\Web;

use App\Infrastructure\Persistence\Eloquent\Models\AuditLog;
use App\Infrastructure\Persistence\Eloquent\Models\Complaint;
use App\Infrastructure\Persistence\Eloquent\Models\Opd;
use App\Infrastructure\Persistence\Eloquent\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ComplaintDeletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_kominfo_can_soft_delete_complaint_and_audit_is_preserved(): void
    {
        $this->seed();

        $citizen = User::query()->where('email', 'masyarakat@gmail.com')->firstOrFail();
        $kominfo = User::query()->where('email', 'kominfo@gmail.com')->firstOrFail();
        $opd = User::query()->where('email', 'opd@gmail.com')->firstOrFail();

        $this->actingAs($citizen)->post('/pengaduan', [
            'title' => 'Laporan untuk dihapus',
            'category' => 'Infrastruktur',
            'description' => 'Laporan uji penghapusan.',
            'target_type' => 'opd',
            'target_id' => Opd::query()->firstOrFail()->id,
        ])->assertRedirect();

        $complaint = Complaint::query()->firstOrFail();
        $url = "/dashboard/complaints/{$complaint->id}";

        $this->actingAs($citizen)->delete($url)->assertForbidden();
        $this->actingAs($opd)->delete($url)->assertForbidden();
        $this->assertDatabaseHas('complaints', ['id' => $complaint->id, 'deleted_at' => null]);

        $this->actingAs($kominfo)->get('/dashboard/complaints')
            ->assertOk()->assertSee('Hapus pengaduan '.$complaint->ticket_number);
        $this->actingAs($kominfo)->get($url)
            ->assertOk()->assertSee('Hapus Pengaduan');

        $this->actingAs($kominfo)->delete($url)
            ->assertRedirect('/dashboard/complaints');

        $this->assertSoftDeleted('complaints', ['id' => $complaint->id]);
        $this->assertDatabaseHas('complaint_status_histories', ['complaint_id' => $complaint->id]);
        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $kominfo->id,
            'action' => 'Pengaduan Dihapus',
            'model_type' => 'complaint',
            'model_id' => $complaint->id,
        ]);
        $this->actingAs($kominfo)->get($url)->assertNotFound();
    }
}
