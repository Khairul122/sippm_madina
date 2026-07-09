<?php

declare(strict_types=1);

namespace Tests\Feature\Web;

use App\Infrastructure\Persistence\Eloquent\Models\AuditLog;
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

        // FR-36: "kelola pengguna" (create) wajib tercatat di audit log.
        $createLog = AuditLog::query()
            ->where('action', 'user_created')
            ->where('model_id', $newUser->id)
            ->firstOrFail();
        $this->assertNull($createLog->old_data);
        $this->assertSame('opd', $createLog->new_data['role']);

        $this->actingAs($kominfo)->post("/dashboard/users/{$newUser->id}/toggle-active")
            ->assertRedirect();

        $this->assertFalse($newUser->fresh()->is_active);

        // FR-36: nonaktifkan akun juga wajib tercatat, dengan old/new
        // data yang benar-benar berbeda (bukan cuma status baru).
        $deactivateLog = AuditLog::query()
            ->where('action', 'user_deactivated')
            ->where('model_id', $newUser->id)
            ->firstOrFail();
        $this->assertSame(['is_active' => true], $deactivateLog->old_data);
        $this->assertSame(['is_active' => false], $deactivateLog->new_data);
    }

    public function test_kominfo_update_records_audit_log_with_old_and_new_data(): void
    {
        $this->seed();

        $kominfo = User::query()->where('email', 'kominfo@demo.test')->firstOrFail();
        $opdUser = User::query()->where('email', 'opd@demo.test')->firstOrFail();

        $this->actingAs($kominfo)->put("/dashboard/users/{$opdUser->id}", [
            'name' => 'OPD Diperbarui',
        ])->assertRedirect('/dashboard/users');

        $this->assertSame('OPD Diperbarui', $opdUser->fresh()->name);

        $updateLog = AuditLog::query()
            ->where('action', 'user_updated')
            ->where('model_id', $opdUser->id)
            ->firstOrFail();
        $this->assertSame($opdUser->name, $updateLog->old_data['name']);
        $this->assertSame('OPD Diperbarui', $updateLog->new_data['name']);
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
