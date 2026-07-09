<?php

declare(strict_types=1);

namespace Tests\Feature\Web;

use App\Infrastructure\Persistence\Eloquent\Models\Complaint;
use App\Infrastructure\Persistence\Eloquent\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Halaman publik (beranda, lacak pengaduan, kegiatan) — sebelumnya tidak
 * ada test sama sekali (progress.md). Ketiganya harus bisa diakses tanpa
 * login sama sekali (routes/web.php: tidak ada middleware auth).
 */
class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_is_accessible_without_login(): void
    {
        $this->seed();

        $this->get('/')->assertOk()->assertSee('SIPPM Madina');
    }

    public function test_activity_feed_page_is_accessible_without_login(): void
    {
        $this->seed();

        $this->get('/kegiatan')->assertOk();
    }

    public function test_track_complaint_page_is_accessible_without_login(): void
    {
        $this->seed();

        $this->get('/lacak')->assertOk();
    }

    public function test_track_complaint_finds_complaint_by_ticket_number(): void
    {
        $this->seed();

        $masyarakat = User::query()->where('email', 'masyarakat@demo.test')->firstOrFail();

        $this->actingAs($masyarakat)->post('/pengaduan', [
            'title' => 'Lampu Jalan Mati',
            'category' => 'Infrastruktur',
            'description' => 'Lampu jalan tidak menyala sejak seminggu lalu.',
            'target_type' => 'bupati',
        ]);

        $complaint = Complaint::query()->firstOrFail();

        $this->get('/lacak?ticket_number='.$complaint->ticket_number)
            ->assertOk()
            ->assertSee('Lampu Jalan Mati');
    }

    public function test_track_complaint_with_unknown_ticket_number_shows_no_result(): void
    {
        $this->seed();

        $this->get('/lacak?ticket_number=TIKET-TIDAK-ADA')
            ->assertOk()
            ->assertDontSee('Lampu Jalan Mati');
    }
}
