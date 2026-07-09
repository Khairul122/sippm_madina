<?php

declare(strict_types=1);

namespace Tests\Feature\Web;

use App\Infrastructure\Persistence\Eloquent\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Halaman Audit Log (/dashboard/audit-log) — sebelumnya tidak ada test
 * sama sekali (progress.md). Fokus: akses kominfo-only, dan isinya berisi
 * jejak aksi nyata (login) alih-alih halaman kosong.
 */
class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_kominfo_can_view_audit_log(): void
    {
        $this->seed();

        $kominfo = User::query()->where('email', 'kominfo@demo.test')->firstOrFail();

        $this->actingAs($kominfo)->get('/dashboard/audit-log')->assertOk();
    }

    public function test_non_kominfo_roles_cannot_view_audit_log(): void
    {
        $this->seed();

        $opdUser = User::query()->where('email', 'opd@demo.test')->firstOrFail();
        $bupati = User::query()->where('email', 'bupati@demo.test')->firstOrFail();

        $this->actingAs($opdUser)->get('/dashboard/audit-log')->assertForbidden();
        $this->actingAs($bupati)->get('/dashboard/audit-log')->assertForbidden();
    }

    public function test_audit_log_records_login_events(): void
    {
        $this->seed();

        $kominfo = User::query()->where('email', 'kominfo@demo.test')->firstOrFail();

        $this->post('/login', [
            'email' => 'kominfo@demo.test',
            'password' => 'password',
        ]);

        $this->actingAs($kominfo)->get('/dashboard/audit-log')
            ->assertOk()
            ->assertSee($kominfo->name)
            ->assertDontSee('Belum ada catatan audit log.');
    }
}
