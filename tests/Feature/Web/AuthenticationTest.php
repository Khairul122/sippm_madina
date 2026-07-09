<?php

declare(strict_types=1);

namespace Tests\Feature\Web;

use App\Infrastructure\Persistence\Eloquent\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Login/register/logout (routes/web.php) — sebelumnya tidak ada test
 * sama sekali (progress.md). Fokus: kredensial salah, redirect
 * role-aware setelah login, throttle:login (5/menit), dan alur
 * pendaftaran masyarakat baru.
 */
class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_with_correct_credentials(): void
    {
        $this->seed();

        $kominfo = User::query()->where('email', 'kominfo@demo.test')->firstOrFail();

        $this->post('/login', [
            'email' => 'kominfo@demo.test',
            'password' => 'password',
        ])->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs($kominfo);
    }

    public function test_masyarakat_is_redirected_to_pengaduan_after_login(): void
    {
        $this->seed();

        $this->post('/login', [
            'email' => 'masyarakat@demo.test',
            'password' => 'password',
        ])->assertRedirect('/pengaduan');
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $this->seed();

        $this->post('/login', [
            'email' => 'kominfo@demo.test',
            'password' => 'salah-password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_login_is_throttled_after_five_attempts(): void
    {
        $this->seed();

        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', [
                'email' => 'kominfo@demo.test',
                'password' => 'salah-password',
            ]);
        }

        // Percobaan ke-6, meski password benar, tetap diblok RateLimiter.
        $response = $this->post('/login', [
            'email' => 'kominfo@demo.test',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_authenticated_user_can_logout(): void
    {
        $this->seed();

        $kominfo = User::query()->where('email', 'kominfo@demo.test')->firstOrFail();

        $this->actingAs($kominfo)->post('/logout')->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_guest_can_register_as_masyarakat_and_is_auto_logged_in(): void
    {
        $this->seed();

        $response = $this->post('/register', [
            'name' => 'Warga Baru',
            'nik' => '1234567890123456',
            'phone' => '081234567890',
            'email' => 'warga.baru@demo.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'consent' => '1',
        ]);

        $response->assertRedirect('/dashboard');

        $user = User::query()->where('email', 'warga.baru@demo.test')->firstOrFail();
        $this->assertAuthenticatedAs($user);
        $this->assertTrue($user->hasRole('masyarakat'));
    }

    public function test_registration_requires_consent(): void
    {
        $this->seed();

        $this->post('/register', [
            'name' => 'Warga Tanpa Consent',
            'nik' => '1234567890123457',
            'phone' => '081234567891',
            'email' => 'tanpa.consent@demo.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertSessionHasErrors('consent');

        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'tanpa.consent@demo.test']);
    }

    public function test_registration_rejects_duplicate_email(): void
    {
        $this->seed();

        $this->post('/register', [
            'name' => 'Duplikat',
            'nik' => '1234567890123458',
            'phone' => '081234567892',
            'email' => 'kominfo@demo.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'consent' => '1',
        ])->assertSessionHasErrors('email');
    }
}
