<?php

declare(strict_types=1);

namespace Tests\Feature\Web;

use App\Infrastructure\Persistence\Eloquent\Models\Complaint;
use App\Infrastructure\Persistence\Eloquent\Models\Opd;
use App\Infrastructure\Persistence\Eloquent\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Skenario "tolak" (BR-04 diajukan -> ditolak) — sebelumnya cuma jalur
 * happy-path (is_valid=1) yang dites di ComplaintWorkflowTest.
 */
class ComplaintRejectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_kominfo_rejecting_a_complaint_requires_a_reason(): void
    {
        $this->seed();

        $masyarakat = User::query()->where('email', 'masyarakat@demo.test')->firstOrFail();
        $kominfo = User::query()->where('email', 'kominfo@demo.test')->firstOrFail();

        $this->actingAs($masyarakat)->post('/pengaduan', [
            'title' => 'Aduan Tidak Jelas',
            'category' => 'Lainnya',
            'description' => 'Deskripsi asal-asalan.',
            'target_type' => 'opd',
        ]);
        $complaint = Complaint::query()->firstOrFail();

        // Tanpa rejection_reason -> ditolak validasi Form Request.
        $this->actingAs($kominfo)->post("/dashboard/complaints/{$complaint->id}/verify", [
            'is_valid' => '0',
        ])->assertSessionHasErrors('rejection_reason');

        $this->assertSame('diajukan', $complaint->fresh()->status->value);
    }

    public function test_kominfo_can_reject_a_complaint_with_a_reason(): void
    {
        $this->seed();

        $masyarakat = User::query()->where('email', 'masyarakat@demo.test')->firstOrFail();
        $kominfo = User::query()->where('email', 'kominfo@demo.test')->firstOrFail();

        $this->actingAs($masyarakat)->post('/pengaduan', [
            'title' => 'Aduan Duplikat',
            'category' => 'Lainnya',
            'description' => 'Sudah pernah diadukan sebelumnya.',
            'target_type' => 'opd',
        ]);
        $complaint = Complaint::query()->firstOrFail();

        $this->actingAs($kominfo)->post("/dashboard/complaints/{$complaint->id}/verify", [
            'is_valid' => '0',
            'rejection_reason' => 'Pengaduan duplikat dengan tiket sebelumnya.',
        ])->assertRedirect();

        $fresh = $complaint->fresh();
        $this->assertSame('ditolak', $fresh->status->value);
        $this->assertSame('Pengaduan duplikat dengan tiket sebelumnya.', $fresh->rejection_reason);

        // Masyarakat tetap bisa lihat status ditolak & alasannya di halaman sendiri.
        $this->actingAs($masyarakat)->get("/pengaduan/{$complaint->id}")
            ->assertOk()
            ->assertSee('Pengaduan duplikat dengan tiket sebelumnya.');
    }

    public function test_a_rejected_complaint_cannot_be_disposed(): void
    {
        $this->seed();

        $masyarakat = User::query()->where('email', 'masyarakat@demo.test')->firstOrFail();
        $kominfo = User::query()->where('email', 'kominfo@demo.test')->firstOrFail();
        $opdUser = User::query()->where('email', 'opd@demo.test')->firstOrFail();
        $opd = Opd::query()->findOrFail($opdUser->opd_id);

        $this->actingAs($masyarakat)->post('/pengaduan', [
            'title' => 'Aduan Ditolak',
            'category' => 'Lainnya',
            'description' => 'Akan ditolak lalu dicoba didisposisikan.',
            'target_type' => 'opd',
        ]);
        $complaint = Complaint::query()->firstOrFail();

        $this->actingAs($kominfo)->post("/dashboard/complaints/{$complaint->id}/verify", [
            'is_valid' => '0',
            'rejection_reason' => 'Bukan wewenang instansi ini.',
        ]);
        $this->assertSame('ditolak', $complaint->fresh()->status->value);

        // BR-04: tidak ada transisi valid dari "ditolak" -> status manapun,
        // termasuk disposisi (StatusTransitionGuard hanya mengizinkan
        // diverifikasi->diproses, bukan ditolak->diproses).
        $this->actingAs($kominfo)->post("/dashboard/complaints/{$complaint->id}/dispose", [
            'targets' => [['type' => 'opd', 'id' => $opd->id]],
        ])->assertSessionHasErrors();

        $this->assertSame('ditolak', $complaint->fresh()->status->value);
    }
}
