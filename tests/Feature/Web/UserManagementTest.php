<?php

declare(strict_types=1);

namespace Tests\Feature\Web;

use App\Infrastructure\Persistence\Eloquent\Models\Opd;
use App\Infrastructure\Persistence\Eloquent\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_kominfo_can_create_and_deactivate_an_internal_user(): void
    {
        $this->seed();

        $kominfo = User::query()->where('email', 'kominfo@demo.test')->firstOrFail();
        $opd = Opd::query()->firstOrFail();

        $this->actingAs($kominfo)->post('/dashboard/users', [
            'name' => 'OPD Kedua',
            'email' => 'opd-kedua@demo.test',
            'password' => 'password123',
            'role' => 'opd',
            'opd_id' => $opd->id,
        ])->assertRedirect('/dashboard/users');

        $newUser = User::query()->where('email', 'opd-kedua@demo.test')->firstOrFail();
        $this->assertTrue($newUser->hasRole('opd'));
        $this->assertTrue($newUser->is_active);

        $this->actingAs($kominfo)->post("/dashboard/users/{$newUser->id}/toggle-active")
            ->assertRedirect();

        $this->assertFalse($newUser->fresh()->is_active);
    }

    public function test_non_kominfo_role_cannot_access_user_management(): void
    {
        $this->seed();

        $opdUser = User::query()->where('email', 'opd@demo.test')->firstOrFail();

        $this->actingAs($opdUser)->get('/dashboard/users')->assertForbidden();
    }

    public function test_masyarakat_cannot_reach_any_dashboard_route(): void
    {
        $this->seed();

        $masyarakat = User::query()->where('email', 'masyarakat@demo.test')->firstOrFail();

        $this->actingAs($masyarakat)->get('/dashboard/statistik')->assertForbidden();
        $this->actingAs($masyarakat)->get('/dashboard/users')->assertForbidden();
    }
}
