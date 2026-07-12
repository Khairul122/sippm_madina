<?php

declare(strict_types=1);

namespace Tests\Feature\Web;

use App\Infrastructure\Persistence\Eloquent\Models\Activity;
use App\Infrastructure\Persistence\Eloquent\Models\AuditLog;
use App\Infrastructure\Persistence\Eloquent\Models\Opd;
use App\Infrastructure\Persistence\Eloquent\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

/**
 * BR-08: kegiatan yang diinput OPD/Camat wajib melalui verifikasi Kominfo
 * sebelum dipublikasikan kepada masyarakat.
 */
class ActivityWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_full_activity_lifecycle_via_web_dashboard(): void
    {
        $this->seed();

        $camat = User::query()->where('email', 'camat@demo.test')->firstOrFail();
        $kominfo = User::query()->where('email', 'kominfo@demo.test')->firstOrFail();

        $this->actingAs($camat)->post('/dashboard/activities', [
            'title' => 'Gotong Royong',
            'description' => 'Kegiatan bersih desa.',
            'date' => '2026-07-01',
            'location' => 'Balai Desa',
        ])->assertRedirect('/dashboard/activities');

        // DummyReportSeeder already seeds its own OPD/Camat activities,
        // so pick out the one this test just created rather than
        // assuming it's the first row in the table.
        $activity = Activity::query()->where('title', 'Gotong Royong')->firstOrFail();
        $this->assertSame('draft', $activity->status->value);

        $this->actingAs($kominfo)->post("/dashboard/activities/{$activity->id}/verify", [
            'is_valid' => '1',
        ])->assertRedirect();
        $this->assertSame('diverifikasi', $activity->fresh()->status->value);

        $this->actingAs($kominfo)->post("/dashboard/activities/{$activity->id}/publish")
            ->assertRedirect();
        $this->assertSame('dipublikasikan', $activity->fresh()->status->value);

        $this->get('/kegiatan')->assertOk()->assertSee('Gotong Royong');

        // FR-36/FR-37: audit log untuk publikasi kegiatan wajib merekam
        // status SEBELUM perubahan (diverifikasi), bukan cuma status baru.
        $publishLog = AuditLog::query()
            ->where('action', 'App\\Infrastructure\\Broadcasting\\Events\\ActivityPublished')
            ->where('model_id', $activity->id)
            ->latest('id')
            ->firstOrFail();

        $this->assertSame(['status' => 'diverifikasi'], $publishLog->old_data);
        $this->assertSame(['status' => 'dipublikasikan'], $publishLog->new_data);

        // Kominfo dapat menarik kembali kegiatan yang sudah dipublikasikan
        // ke draft (mis. untuk memperbaiki data sebelum dipublikasikan ulang).
        $this->actingAs($kominfo)->post("/dashboard/activities/{$activity->id}/unpublish")
            ->assertRedirect();
        $this->assertSame('draft', $activity->fresh()->status->value);
        $this->get('/kegiatan')->assertOk()->assertDontSee('Gotong Royong');
    }

    public function test_only_published_activity_can_be_unpublished(): void
    {
        $this->seed();

        $kominfo = User::query()->where('email', 'kominfo@demo.test')->firstOrFail();
        $opd = Opd::query()->firstOrFail();
        $activity = Activity::query()->create([
            'title' => 'Kegiatan Draft',
            'description' => 'Uraian kegiatan.',
            'actor_type' => 'opd',
            'actor_id' => $opd->id,
            'date' => '2026-07-01',
            'status' => 'draft',
        ]);

        $this->actingAs($kominfo)->post("/dashboard/activities/{$activity->id}/unpublish")
            ->assertRedirect()
            ->assertSessionHasErrors();
        $this->assertSame('draft', $activity->fresh()->status->value);
    }

    public function test_non_kominfo_cannot_unpublish_activity(): void
    {
        $this->seed();

        $camat = User::query()->where('email', 'camat@demo.test')->firstOrFail();
        $opd = Opd::query()->firstOrFail();
        $activity = Activity::query()->create([
            'title' => 'Kegiatan Terpublikasi',
            'description' => 'Uraian kegiatan.',
            'actor_type' => 'opd',
            'actor_id' => $opd->id,
            'date' => '2026-07-01',
            'status' => 'dipublikasikan',
        ]);

        $this->actingAs($camat)->post("/dashboard/activities/{$activity->id}/unpublish")
            ->assertForbidden();
        $this->assertSame('dipublikasikan', $activity->fresh()->status->value);
    }

    /**
     * FR-23/FR-24: filter kegiatan berdasarkan status dan tujuan
     * (OPD/Kecamatan) untuk role yang melihat semua kegiatan (Kominfo).
     */
    public function test_activity_index_filters_by_status_and_target(): void
    {
        $this->seed();

        $camat = User::query()->where('email', 'camat@demo.test')->firstOrFail();
        $kominfo = User::query()->where('email', 'kominfo@demo.test')->firstOrFail();
        $opd = Opd::query()->firstOrFail();

        $this->actingAs($camat)->post('/dashboard/activities', [
            'title' => 'Kegiatan Kecamatan',
            'description' => 'Kegiatan milik kecamatan.',
            'date' => '2026-07-01',
            'location' => 'Kantor Camat',
        ]);

        $activity = Activity::query()->where('title', 'Kegiatan Kecamatan')->firstOrFail();

        // Filter status=draft harus menampilkan kegiatan ini (masih draft).
        $this->actingAs($kominfo)
            ->get('/dashboard/activities?status=draft')
            ->assertOk()
            ->assertSee('Kegiatan Kecamatan');

        // Filter status=dipublikasikan TIDAK boleh menampilkan kegiatan
        // yang masih draft.
        $this->actingAs($kominfo)
            ->get('/dashboard/activities?status=dipublikasikan')
            ->assertOk()
            ->assertDontSee('Kegiatan Kecamatan');

        // Filter target=opd:{id} tidak boleh menampilkan kegiatan yang
        // dibuat oleh kecamatan.
        $this->actingAs($kominfo)
            ->get('/dashboard/activities?target=opd:'.$opd->id)
            ->assertOk()
            ->assertDontSee('Kegiatan Kecamatan');
    }

    /**
     * Regression: without lang/id/validation.php, APP_LOCALE=id (and no
     * English fallback, since APP_FALLBACK_LOCALE is also id) made every
     * validation error render as its raw untranslated key, e.g. the
     * literal string "validation.max.file" instead of a readable
     * Indonesian message.
     */
    public function test_oversized_documentation_upload_shows_localized_error_message(): void
    {
        $this->seed();

        $opd = User::query()->where('email', 'opd@demo.test')->firstOrFail();

        $response = $this->actingAs($opd)->post('/dashboard/activities', [
            'title' => 'Kegiatan Uji Ukuran Berkas',
            'description' => 'Uji validasi ukuran lampiran.',
            'date' => '2026-07-01',
            'location' => 'Kantor OPD',
            'documentations' => [UploadedFile::fake()->create('bukti.jpg', 6000, 'image/jpeg')],
        ]);

        $response->assertSessionHasErrors('documentations.0');
        $errors = session('errors')->getBag('default');
        $message = $errors->first('documentations.0');

        $this->assertStringNotContainsString('validation.', $message);
        $this->assertStringContainsString('kilobyte', $message);
    }

    public function test_kominfo_cannot_input_activity_directly(): void
    {
        $this->seed();

        $kominfo = User::query()->where('email', 'kominfo@demo.test')->firstOrFail();

        $this->actingAs($kominfo)->post('/dashboard/activities', [
            'title' => 'Kegiatan Kominfo',
            'description' => 'Tidak boleh.',
            'date' => '2026-07-01',
        ])->assertForbidden();
    }
}
