<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Infrastructure\Persistence\Eloquent\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RateLimitTest extends TestCase
{
    use RefreshDatabase;

    public function test_site_limit_covers_unknown_urls_and_isolated_by_ip(): void
    {
        $this->withServerVariables(['REMOTE_ADDR' => '8.8.8.8']);

        for ($i = 0; $i < 120; $i++) {
            $this->get('/not-a-route')->assertNotFound();
        }

        $this->get('/not-a-route')->assertTooManyRequests();
        $this->withServerVariables(['REMOTE_ADDR' => '9.9.9.9'])->get('/')->assertOk();
    }

    public function test_registration_limit_is_shared_by_web_and_api(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post('/register', [])->assertSessionHasErrors();
            $this->postJson('/api/v1/register', [])->assertUnprocessable();
        }

        $this->postJson('/api/v1/register', [])->assertTooManyRequests();
    }

    public function test_complaint_submission_is_limited_per_account(): void
    {
        $this->seed();
        $citizen = User::query()->where('email', 'masyarakat@gmail.com')->firstOrFail();

        for ($i = 0; $i < 10; $i++) {
            $this->actingAs($citizen)->post('/pengaduan', [])->assertSessionHasErrors();
        }

        $this->actingAs($citizen)->post('/pengaduan', [])->assertTooManyRequests();
    }

    public function test_tracking_limit_is_shared_by_web_and_api(): void
    {
        for ($i = 0; $i < 30; $i++) {
            $this->get('/lacak')->assertOk();
        }

        $this->getJson('/api/v1/track/PGD-2026-000001')->assertTooManyRequests();
    }

    public function test_login_limit_cannot_be_bypassed_by_changing_email(): void
    {
        $this->seed();

        for ($i = 0; $i < 20; $i++) {
            $this->post('/login', [
                'email' => "missing{$i}@example.com",
                'password' => 'wrong-password',
            ])->assertSessionHasErrors('email');
        }

        $this->post('/login', [
            'email' => 'kominfo@gmail.com',
            'password' => 'sipapa12345678',
        ])->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}
