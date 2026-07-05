<?php

declare(strict_types=1);

namespace Tests\Feature\Web;

use App\Infrastructure\Persistence\Eloquent\Models\Activity;
use App\Infrastructure\Persistence\Eloquent\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

        $activity = Activity::query()->firstOrFail();
        $this->assertSame('draft', $activity->status->value);

        $this->actingAs($kominfo)->post("/dashboard/activities/{$activity->id}/verify", [
            'is_valid' => '1',
        ])->assertRedirect();
        $this->assertSame('diverifikasi', $activity->fresh()->status->value);

        $this->actingAs($kominfo)->post("/dashboard/activities/{$activity->id}/publish")
            ->assertRedirect();
        $this->assertSame('dipublikasikan', $activity->fresh()->status->value);

        $this->get('/kegiatan')->assertOk()->assertSee('Gotong Royong');
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
