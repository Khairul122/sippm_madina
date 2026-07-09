<?php

declare(strict_types=1);

namespace Tests\Feature\Web;

use App\Infrastructure\Persistence\Eloquent\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Export statistik PDF/Excel (StatisticsController::exportPdf/exportExcel)
 * — sebelumnya tidak ada test sama sekali (progress.md). Fokus: RBAC
 * (masyarakat tidak boleh, semua role internal boleh) dan tipe berkas
 * yang benar-benar dihasilkan, bukan cuma bahwa route tidak error 500.
 */
class StatisticsExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_kominfo_can_export_statistics_as_pdf(): void
    {
        $this->seed();

        $kominfo = User::query()->where('email', 'kominfo@demo.test')->firstOrFail();

        $response = $this->actingAs($kominfo)->get('/dashboard/statistik/export/pdf');

        $response->assertOk();
        $this->assertSame('application/pdf', $response->headers->get('Content-Type'));
    }

    public function test_kominfo_can_export_statistics_as_excel(): void
    {
        $this->seed();

        $kominfo = User::query()->where('email', 'kominfo@demo.test')->firstOrFail();

        $response = $this->actingAs($kominfo)->get('/dashboard/statistik/export/excel');

        $response->assertOk();
        $this->assertStringContainsString(
            'spreadsheet',
            (string) $response->headers->get('Content-Type'),
        );
    }

    public function test_bupati_can_export_statistics(): void
    {
        $this->seed();

        $bupati = User::query()->where('email', 'bupati@demo.test')->firstOrFail();

        $this->actingAs($bupati)->get('/dashboard/statistik/export/pdf')->assertOk();
    }

    public function test_masyarakat_cannot_access_statistics_or_export(): void
    {
        $this->seed();

        $masyarakat = User::query()->where('email', 'masyarakat@demo.test')->firstOrFail();

        $this->actingAs($masyarakat)->get('/dashboard/statistik')->assertForbidden();
        $this->actingAs($masyarakat)->get('/dashboard/statistik/export/pdf')->assertForbidden();
        $this->actingAs($masyarakat)->get('/dashboard/statistik/export/excel')->assertForbidden();
    }
}
