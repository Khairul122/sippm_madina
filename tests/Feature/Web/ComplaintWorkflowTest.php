<?php

declare(strict_types=1);

namespace Tests\Feature\Web;

use App\Infrastructure\Persistence\Eloquent\Models\AuditLog;
use App\Infrastructure\Persistence\Eloquent\Models\Complaint;
use App\Infrastructure\Persistence\Eloquent\Models\Opd;
use App\Infrastructure\Persistence\Eloquent\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * End-to-end web (session-based) walk of the full complaint lifecycle:
 * masyarakat ajukan -> kominfo verifikasi -> kominfo disposisi -> OPD
 * tangani -> kominfo jawab resmi. Mirrors the API feature tests but drives
 * the Blade/session routes added in Fase 7 (routes/web.php).
 */
class ComplaintWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_full_complaint_lifecycle_via_web_dashboard(): void
    {
        $this->seed();

        $masyarakat = User::query()->where('email', 'masyarakat@gmail.com')->firstOrFail();
        $kominfo = User::query()->where('email', 'kominfo@gmail.com')->firstOrFail();
        $opdUser = User::query()->where('email', 'opd@gmail.com')->firstOrFail();
        $opd = Opd::query()->findOrFail($opdUser->opd_id);

        // 1. Masyarakat mengajukan pengaduan.
        $this->actingAs($masyarakat)->post('/pengaduan', [
            'title' => 'Jalan Rusak',
            'category' => 'Infrastruktur',
            'description' => 'Jalan berlubang di depan rumah.',
            'target_type' => 'opd',
            'target_id' => $opd->id,
        ])->assertRedirect();

        $complaint = Complaint::query()->firstOrFail();
        $this->assertSame('diajukan', $complaint->status->value);

        // Masyarakat tidak boleh mengakses dashboard internal.
        $this->actingAs($masyarakat)->get('/dashboard/complaints')->assertForbidden();

        // 2. Kominfo memverifikasi.
        $this->actingAs($kominfo)->post("/dashboard/complaints/{$complaint->id}/verify", [
            'is_valid' => '1',
        ])->assertRedirect();
        $this->assertSame('diverifikasi', $complaint->fresh()->status->value);

        // 3. Kominfo mendisposisikan ke OPD.
        $this->actingAs($kominfo)->post("/dashboard/complaints/{$complaint->id}/dispose", [
            'targets' => [['type' => 'opd', 'id' => $opd->id]],
        ])->assertRedirect();
        $this->assertSame('diproses', $complaint->fresh()->status->value);

        $dispositionId = $complaint->fresh()->dispositions()->firstOrFail()->id;

        // OPD lain (bukan tujuan disposisi) tidak boleh menangani.
        $otherOpdUser = User::query()->where('email', 'camat@gmail.com')->firstOrFail();
        $this->actingAs($otherOpdUser)->post("/dashboard/complaints/{$complaint->id}/handle", [
            'disposition_id' => $dispositionId,
            'description' => 'Mencoba menangani tanpa disposisi.',
        ])->assertForbidden();

        // 4. OPD tujuan menangani.
        $this->actingAs($opdUser)->post("/dashboard/complaints/{$complaint->id}/handle", [
            'disposition_id' => $dispositionId,
            'description' => 'Sudah diperbaiki.',
        ])->assertRedirect();
        $this->assertSame('ditindaklanjuti', $complaint->fresh()->status->value);

        // 5. Kominfo mengirim jawaban resmi.
        $this->actingAs($kominfo)->post("/dashboard/complaints/{$complaint->id}/respond", [
            'response_text' => 'Terima kasih, pengaduan telah selesai ditangani.',
        ])->assertRedirect();
        $this->assertSame('selesai', $complaint->fresh()->status->value);

        $this->assertDatabaseHas('complaint_responses', [
            'complaint_id' => $complaint->id,
        ]);

        // Masyarakat dapat melihat pengaduannya sendiri.
        $this->actingAs($masyarakat)->get("/pengaduan/{$complaint->id}")
            ->assertOk()
            ->assertSee('Terima kasih, pengaduan telah selesai ditangani.');

        // FR-36/FR-37: setiap baris audit log transisi status pengaduan
        // wajib merekam status SEBELUM perubahan (old_data), tidak cuma
        // status baru — sebelumnya old_data selalu null di semua baris.
        $verifyLog = AuditLog::query()
            ->where('action', 'Pengaduan Diverifikasi')
            ->where('model_id', $complaint->id)
            ->firstOrFail();
        $this->assertSame(['status' => 'diajukan'], $verifyLog->old_data);

        $disposeLog = AuditLog::query()
            ->where('action', 'Pengaduan Didisposisikan')
            ->where('model_id', $complaint->id)
            ->firstOrFail();
        $this->assertSame(['status' => 'diverifikasi'], $disposeLog->old_data);

        $handleLog = AuditLog::query()
            ->where('action', 'Pengaduan Ditindaklanjuti')
            ->where('model_id', $complaint->id)
            ->firstOrFail();
        $this->assertSame(['status' => 'diproses'], $handleLog->old_data);

        $resolveLog = AuditLog::query()
            ->where('action', 'Pengaduan Selesai & Ditanggap')
            ->where('model_id', $complaint->id)
            ->firstOrFail();
        $this->assertSame(['status' => 'ditindaklanjuti'], $resolveLog->old_data);
    }

    public function test_kominfo_can_cancel_wrong_disposition_and_redispose(): void
    {
        $this->seed();

        $masyarakat = User::query()->where('email', 'masyarakat@gmail.com')->firstOrFail();
        $kominfo = User::query()->where('email', 'kominfo@gmail.com')->firstOrFail();
        $opdUser = User::query()->where('email', 'opd@gmail.com')->firstOrFail();
        $wrongOpd = Opd::query()->findOrFail($opdUser->opd_id);
        $correctOpd = Opd::query()->where('id', '!=', $wrongOpd->id)->firstOrFail();

        $this->actingAs($masyarakat)->post('/pengaduan', [
            'title' => 'Salah Disposisi',
            'category' => 'Infrastruktur',
            'description' => 'Pengaduan untuk uji batal disposisi.',
            'target_type' => 'opd',
            'target_id' => $wrongOpd->id,
        ]);

        $complaint = Complaint::query()->firstOrFail();
        $this->actingAs($kominfo)->post("/dashboard/complaints/{$complaint->id}/verify", ['is_valid' => '1']);

        // Kominfo salah pilih target.
        $this->actingAs($kominfo)->post("/dashboard/complaints/{$complaint->id}/dispose", [
            'targets' => [['type' => 'opd', 'id' => $wrongOpd->id]],
        ])->assertRedirect();
        $this->assertSame('diproses', $complaint->fresh()->status->value);

        // OPD yang salah tidak boleh lagi menangani setelah dibatalkan.
        $this->actingAs($kominfo)->post("/dashboard/complaints/{$complaint->id}/cancel-disposition", [
            'note' => 'salah pilih OPD tujuan',
        ])->assertRedirect();

        $complaint->refresh();
        $this->assertSame('diverifikasi', $complaint->status->value);
        $this->assertNotNull($complaint->dispositions()->first()->cancelled_at);

        $this->actingAs($opdUser)->post("/dashboard/complaints/{$complaint->id}/handle", [
            'disposition_id' => $complaint->dispositions()->first()->id,
            'description' => 'Mencoba menangani disposisi yang sudah dibatalkan.',
        ])->assertForbidden();

        // Kominfo disposisikan ulang ke OPD yang benar.
        $this->actingAs($kominfo)->post("/dashboard/complaints/{$complaint->id}/dispose", [
            'targets' => [['type' => 'opd', 'id' => $correctOpd->id]],
        ])->assertRedirect();
        $this->assertSame('diproses', $complaint->fresh()->status->value);
        $this->assertSame(2, $complaint->dispositions()->count());
    }

    public function test_kominfo_cannot_dispose_directly_to_bupati(): void
    {
        $this->seed();

        $masyarakat = User::query()->where('email', 'masyarakat@gmail.com')->firstOrFail();
        $kominfo = User::query()->where('email', 'kominfo@gmail.com')->firstOrFail();

        $this->actingAs($masyarakat)->post('/pengaduan', [
            'title' => 'Aduan untuk Bupati',
            'category' => 'Lainnya',
            'description' => 'Aduan ditujukan ke Bupati.',
            'target_type' => 'bupati',
        ]);

        $complaint = Complaint::query()->firstOrFail();
        $this->actingAs($kominfo)->post("/dashboard/complaints/{$complaint->id}/verify", ['is_valid' => '1']);

        // BR-01: disposisi hanya boleh ke opd/camat, walau target asli Bupati.
        $this->actingAs($kominfo)->post("/dashboard/complaints/{$complaint->id}/dispose", [
            'targets' => [['type' => 'bupati', 'id' => 1]],
        ])->assertSessionHasErrors();

        $this->assertSame('diverifikasi', $complaint->fresh()->status->value);
    }
}
