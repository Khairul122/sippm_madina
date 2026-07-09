<?php

declare(strict_types=1);

namespace Tests\Feature\Web;

use App\Infrastructure\Persistence\Eloquent\Models\Complaint;
use App\Infrastructure\Persistence\Eloquent\Models\Opd;
use App\Infrastructure\Persistence\Eloquent\Models\TtdSignature;
use App\Infrastructure\Persistence\Eloquent\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class LaporanTest extends TestCase
{
    use RefreshDatabase;

    public function test_kominfo_can_view_laporan_page(): void
    {
        $this->seed();

        $kominfo = User::query()->where('email', 'kominfo@demo.test')->firstOrFail();

        $this->actingAs($kominfo)->get('/dashboard/laporan')->assertOk();
    }

    public function test_non_kominfo_role_cannot_view_laporan(): void
    {
        $this->seed();

        $opdUser = User::query()->where('email', 'opd@demo.test')->firstOrFail();

        $this->actingAs($opdUser)->get('/dashboard/laporan')->assertForbidden();
    }

    public function test_kominfo_can_filter_laporan_by_status_and_tujuan(): void
    {
        $this->seed();

        $kominfo = User::query()->where('email', 'kominfo@demo.test')->firstOrFail();
        $masyarakat = User::query()->where('email', 'masyarakat@demo.test')->firstOrFail();
        $opd = Opd::query()->firstOrFail();

        $matching = $this->makeComplaint($masyarakat, $opd->id, 'diajukan');
        $this->makeComplaint($masyarakat, $opd->id, 'selesai');

        $response = $this->actingAs($kominfo)->get('/dashboard/laporan?status=diajukan&target=opd:'.$opd->id);

        $response->assertOk();
        $response->assertSee($matching->ticket_number);
    }

    public function test_hari_filter_is_independent_from_bulan_and_tahun(): void
    {
        $this->seed();

        $kominfo = User::query()->where('email', 'kominfo@demo.test')->firstOrFail();
        $masyarakat = User::query()->where('email', 'masyarakat@demo.test')->firstOrFail();
        $opd = Opd::query()->firstOrFail();

        // Dua tanggal Senin (weekday=0) di bulan berbeda, dihitung relatif
        // supaya test tidak bergantung pada kalender riil tanggal tertentu.
        $senin1 = Carbon::now()->subMonths(2)->startOfMonth()->next(Carbon::MONDAY);
        $senin2 = Carbon::now()->startOfMonth()->next(Carbon::MONDAY);
        $selasa = $senin2->copy()->addDay();

        $complaintSenin1 = $this->makeComplaint($masyarakat, $opd->id, 'diajukan', $senin1);
        $complaintSenin2 = $this->makeComplaint($masyarakat, $opd->id, 'diajukan', $senin2);
        $complaintSelasa = $this->makeComplaint($masyarakat, $opd->id, 'diajukan', $selasa);

        $response = $this->actingAs($kominfo)->get('/dashboard/laporan?hari=0');

        $response->assertOk();
        $response->assertSee($complaintSenin1->ticket_number);
        $response->assertSee($complaintSenin2->ticket_number);
        $response->assertDontSee($complaintSelasa->ticket_number);
    }

    public function test_kominfo_can_save_and_update_ttd_signature(): void
    {
        $this->seed();

        $kominfo = User::query()->where('email', 'kominfo@demo.test')->firstOrFail();

        $payload = [
            'nama_penandatangan' => 'Budi Santoso',
            'jabatan_penandatangan' => 'Kepala Dinas Komunikasi dan Informatika',
            'pangkat' => 'Pembina Utama Muda',
            'nip' => '196501011990031001',
        ];

        $this->actingAs($kominfo)->post('/dashboard/laporan/ttd', $payload)
            ->assertRedirect('/dashboard/laporan');

        $this->assertDatabaseCount('ttd_signatures', 1);
        $this->assertSame('Budi Santoso', TtdSignature::query()->firstOrFail()->nama_penandatangan);

        $this->actingAs($kominfo)->post('/dashboard/laporan/ttd', array_merge($payload, [
            'nama_penandatangan' => 'Siti Aminah',
        ]))->assertRedirect('/dashboard/laporan');

        $this->assertDatabaseCount('ttd_signatures', 1);
        $this->assertSame('Siti Aminah', TtdSignature::query()->firstOrFail()->nama_penandatangan);
    }

    public function test_ttd_update_validation_rejects_missing_required_fields(): void
    {
        $this->seed();

        $kominfo = User::query()->where('email', 'kominfo@demo.test')->firstOrFail();

        $this->actingAs($kominfo)->post('/dashboard/laporan/ttd', [
            'jabatan_penandatangan' => 'Kepala Dinas',
        ])->assertSessionHasErrors(['nama_penandatangan', 'nip']);

        $this->assertDatabaseCount('ttd_signatures', 0);
    }

    public function test_ttd_nip_no_longer_requires_exactly_18_digits(): void
    {
        $this->seed();

        $kominfo = User::query()->where('email', 'kominfo@demo.test')->firstOrFail();

        $this->actingAs($kominfo)->post('/dashboard/laporan/ttd', [
            'nama_penandatangan' => 'Budi Santoso',
            'jabatan_penandatangan' => 'Kepala Dinas',
            'nip' => '123',
        ])->assertSessionDoesntHaveErrors('nip');

        $this->assertDatabaseCount('ttd_signatures', 1);
        $this->assertSame('123', TtdSignature::query()->firstOrFail()->nip);
    }

    public function test_export_pdf_and_excel_return_success(): void
    {
        $this->seed();

        $kominfo = User::query()->where('email', 'kominfo@demo.test')->firstOrFail();
        $masyarakat = User::query()->where('email', 'masyarakat@demo.test')->firstOrFail();
        $opd = Opd::query()->firstOrFail();
        $this->makeComplaint($masyarakat, $opd->id, 'diajukan');

        $this->actingAs($kominfo)->get('/dashboard/laporan/export-pdf')
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        $this->actingAs($kominfo)->get('/dashboard/laporan/export-excel')
            ->assertOk();
    }

    private function makeComplaint(User $user, int $opdId, string $status, ?Carbon $createdAt = null): Complaint
    {
        static $sequence = 100;
        $sequence++;

        $complaint = Complaint::query()->create([
            'ticket_number' => 'PGD-2026-'.str_pad((string) $sequence, 6, '0', STR_PAD_LEFT),
            'user_id' => $user->id,
            'title' => 'Laporan Uji '.$sequence,
            'description' => 'Deskripsi uji coba.',
            'category' => 'Infrastruktur',
            'target_type' => 'opd',
            'target_id' => $opdId,
            'status' => $status,
        ]);

        if ($createdAt !== null) {
            $complaint->forceFill(['created_at' => $createdAt])->save();
        }

        return $complaint->fresh();
    }
}
