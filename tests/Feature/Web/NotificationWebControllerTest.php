<?php

declare(strict_types=1);

namespace Tests\Feature\Web;

use App\Infrastructure\Persistence\Eloquent\Models\Notification;
use App\Infrastructure\Persistence\Eloquent\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * FR-34: indikator jumlah notifikasi belum dibaca. Sebelumnya dihitung
 * client-side dari 20 notifikasi terbaru saja (per_page endpoint), jadi
 * salah kalau user punya lebih dari 20 notifikasi belum dibaca.
 */
class NotificationWebControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_unread_count_reflects_all_unread_rows_not_just_the_paginated_page(): void
    {
        $this->seed();

        $kominfo = User::query()->where('email', 'kominfo@demo.test')->firstOrFail();

        // Seeder demo (DummyReportSeeder) sudah bisa menyisakan beberapa
        // notifikasi belum dibaca untuk kominfo — hitung baseline dulu,
        // baru bandingkan pertambahannya, supaya test tidak rapuh
        // terhadap jumlah data seeder yang berubah di masa depan.
        $baseline = Notification::query()->where('user_id', $kominfo->id)->where('is_read', false)->count();

        // 25 notifikasi belum dibaca baru > per_page=20 pada endpoint index.
        for ($i = 1; $i <= 25; $i++) {
            Notification::query()->create([
                'user_id' => $kominfo->id,
                'title' => "Notifikasi {$i}",
                'message' => 'Uji unread count.',
                'type' => 'TestNotification',
                'is_read' => false,
                'data' => null,
            ]);
        }

        $response = $this->actingAs($kominfo)
            ->getJson('/dashboard/notifications')
            ->assertOk();

        $this->assertCount(20, $response->json('data'));
        $this->assertSame($baseline + 25, $response->json('unread_count'));
    }

    public function test_unread_count_decreases_after_marking_all_read(): void
    {
        $this->seed();

        $kominfo = User::query()->where('email', 'kominfo@demo.test')->firstOrFail();

        Notification::query()->create([
            'user_id' => $kominfo->id,
            'title' => 'Notifikasi',
            'message' => 'Uji.',
            'type' => 'TestNotification',
            'is_read' => false,
            'data' => null,
        ]);

        $this->actingAs($kominfo)->postJson('/dashboard/notifications/read-all')->assertOk();

        $this->actingAs($kominfo)
            ->getJson('/dashboard/notifications')
            ->assertOk()
            ->assertJson(['unread_count' => 0]);
    }
}
