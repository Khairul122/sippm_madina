<?php

declare(strict_types=1);

namespace Tests\Feature\Web;

use App\Infrastructure\Persistence\Eloquent\Models\AuditLog;
use App\Infrastructure\Persistence\Eloquent\Models\BlockedIp;
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

        $kominfo = User::query()->where('email', 'kominfo@gmail.com')->firstOrFail();

        $this->actingAs($kominfo)->get('/dashboard/audit-log')->assertOk();
    }

    public function test_non_kominfo_roles_cannot_view_audit_log(): void
    {
        $this->seed();

        $opdUser = User::query()->where('email', 'opd@gmail.com')->firstOrFail();
        $bupati = User::query()->where('email', 'bupati@gmail.com')->firstOrFail();

        $this->actingAs($opdUser)->get('/dashboard/audit-log')->assertForbidden();
        $this->actingAs($bupati)->get('/dashboard/audit-log')->assertForbidden();
    }

    public function test_audit_log_records_login_events(): void
    {
        $this->seed();

        $kominfo = User::query()->where('email', 'kominfo@gmail.com')->firstOrFail();

        $this->post('/login', [
            'email' => 'kominfo@gmail.com',
            'password' => 'sipapa12345678',
        ]);

        $this->actingAs($kominfo)->get('/dashboard/audit-log')
            ->assertOk()
            ->assertSee($kominfo->name)
            ->assertSee('Login Berhasil')
            ->assertDontSee('Belum ada catatan audit log.');
    }

    public function test_kominfo_can_block_and_unblock_ip_from_audit_log(): void
    {
        $this->seed();
        $kominfo = User::query()->where('email', 'kominfo@gmail.com')->firstOrFail();
        $log = AuditLog::query()->create([
            'action' => 'Gagal Login',
            'model_type' => 'user',
            'ip_address' => '8.8.8.8',
        ]);

        $this->actingAs($kominfo)->get('/dashboard/audit-log')
            ->assertOk()->assertSee('Blokir IP');

        $this->actingAs($kominfo)->post("/dashboard/audit-log/{$log->id}/block-ip")
            ->assertRedirect();

        $block = BlockedIp::query()->where('ip_address', '8.8.8.8')->firstOrFail();
        $this->assertDatabaseHas('audit_logs', ['action' => 'IP Diblokir', 'model_id' => $block->id]);
        $this->actingAs($kominfo)->get('/dashboard/audit-log')
            ->assertOk()->assertSee('Buka Blokir');
        $this->withServerVariables(['REMOTE_ADDR' => '8.8.8.8'])->get('/')->assertForbidden();
        $this->withServerVariables(['REMOTE_ADDR' => '8.8.8.8'])->getJson('/api/complaints')->assertForbidden();

        $this->withServerVariables(['REMOTE_ADDR' => '9.9.9.9'])
            ->actingAs($kominfo)->delete("/dashboard/audit-log/blocked-ips/{$block->id}")
            ->assertRedirect();

        $this->assertDatabaseMissing('blocked_ips', ['id' => $block->id]);
        $this->assertDatabaseHas('audit_logs', ['action' => 'Blokir IP Dicabut', 'model_id' => $block->id]);
        $this->withServerVariables(['REMOTE_ADDR' => '8.8.8.8'])->get('/')->assertOk();
    }

    public function test_non_kominfo_and_current_ip_cannot_be_blocked(): void
    {
        $this->seed();
        $kominfo = User::query()->where('email', 'kominfo@gmail.com')->firstOrFail();
        $opd = User::query()->where('email', 'opd@gmail.com')->firstOrFail();
        $log = AuditLog::query()->create([
            'action' => 'Gagal Login',
            'model_type' => 'user',
            'ip_address' => '8.8.8.8',
        ]);

        $this->withServerVariables(['REMOTE_ADDR' => '9.9.9.9'])
            ->actingAs($opd)->post("/dashboard/audit-log/{$log->id}/block-ip")
            ->assertForbidden();

        $this->withServerVariables(['REMOTE_ADDR' => '8.8.8.8'])
            ->actingAs($kominfo)->post("/dashboard/audit-log/{$log->id}/block-ip")
            ->assertSessionHasErrors();

        $localLog = AuditLog::query()->create([
            'action' => 'Gagal Login',
            'model_type' => 'user',
            'ip_address' => '127.0.0.1',
        ]);
        $this->withServerVariables(['REMOTE_ADDR' => '9.9.9.9'])
            ->actingAs($kominfo)->post("/dashboard/audit-log/{$localLog->id}/block-ip")
            ->assertSessionHasErrors();

        $this->assertDatabaseCount('blocked_ips', 0);
    }

    public function test_audit_ip_does_not_trust_unconfigured_proxy_headers(): void
    {
        $this->withServerVariables([
            'REMOTE_ADDR' => '9.9.9.9',
            'HTTP_CF_CONNECTING_IP' => '8.8.8.8',
        ])->get('/');

        $this->assertSame('9.9.9.9', AuditLog::resolveRealIp());
    }
}
