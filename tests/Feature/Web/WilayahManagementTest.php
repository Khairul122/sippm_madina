<?php

declare(strict_types=1);

namespace Tests\Feature\Web;

use App\Infrastructure\Persistence\Eloquent\Models\Kecamatan;
use App\Infrastructure\Persistence\Eloquent\Models\Opd;
use App\Infrastructure\Persistence\Eloquent\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * CRUD Data Wilayah (OPD/Kecamatan/Desa) — sebelumnya tidak ada test sama
 * sekali (progress.md: "murni CRUD referensi... coverage cukup lewat
 * verifikasi manual"). Fokus di sini: RBAC kominfo-only dan guard
 * anti-orphan-delete, bukan validasi field dasar (sudah dijaga FormRequest).
 */
class WilayahManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_kominfo_can_manage_opd(): void
    {
        $this->seed();

        $kominfo = User::query()->where('email', 'kominfo@demo.test')->firstOrFail();

        $this->actingAs($kominfo)->post('/dashboard/opd', [
            'name' => 'Dinas Uji Coba',
            'code' => 'DUC',
        ])->assertRedirect('/dashboard/opd');

        $opd = Opd::query()->where('code', 'DUC')->firstOrFail();

        $this->actingAs($kominfo)->put("/dashboard/opd/{$opd->id}", [
            'name' => 'Dinas Uji Coba Diperbarui',
            'code' => 'DUC',
        ])->assertRedirect('/dashboard/opd');

        $this->assertSame('Dinas Uji Coba Diperbarui', $opd->fresh()->name);

        $this->actingAs($kominfo)->delete("/dashboard/opd/{$opd->id}")
            ->assertRedirect('/dashboard/opd');

        $this->assertDatabaseMissing('opds', ['id' => $opd->id]);
    }

    public function test_non_kominfo_role_cannot_manage_wilayah(): void
    {
        $this->seed();

        $opdUser = User::query()->where('email', 'opd@demo.test')->firstOrFail();

        $this->actingAs($opdUser)->get('/dashboard/opd')->assertForbidden();
        $this->actingAs($opdUser)->post('/dashboard/opd', ['name' => 'X', 'code' => 'X'])->assertForbidden();
        $this->actingAs($opdUser)->get('/dashboard/kecamatan')->assertForbidden();
        $this->actingAs($opdUser)->get('/dashboard/desa')->assertForbidden();
    }

    public function test_opd_still_assigned_to_a_user_cannot_be_deleted(): void
    {
        $this->seed();

        $kominfo = User::query()->where('email', 'kominfo@demo.test')->firstOrFail();
        $opdUser = User::query()->where('email', 'opd@demo.test')->firstOrFail();
        $opd = Opd::query()->findOrFail($opdUser->opd_id);

        $this->actingAs($kominfo)->delete("/dashboard/opd/{$opd->id}")
            ->assertSessionHasErrors();

        $this->assertDatabaseHas('opds', ['id' => $opd->id]);
    }

    public function test_kecamatan_with_desa_cannot_be_deleted(): void
    {
        $this->seed();

        $kominfo = User::query()->where('email', 'kominfo@demo.test')->firstOrFail();
        $kecamatan = Kecamatan::query()->firstOrFail();

        $this->actingAs($kominfo)->post('/dashboard/desa', [
            'kecamatan_id' => $kecamatan->id,
            'name' => 'Desa Uji Coba',
        ])->assertRedirect('/dashboard/desa');

        $this->actingAs($kominfo)->delete("/dashboard/kecamatan/{$kecamatan->id}")
            ->assertSessionHasErrors();

        $this->assertDatabaseHas('kecamatans', ['id' => $kecamatan->id]);
    }

    public function test_desa_index_can_be_filtered_by_kecamatan(): void
    {
        $this->seed();

        $kominfo = User::query()->where('email', 'kominfo@demo.test')->firstOrFail();
        $kecamatanA = Kecamatan::query()->firstOrFail();
        $kecamatanB = Kecamatan::query()->create(['name' => 'Kecamatan B Uji', 'code' => 'KECB']);

        $this->actingAs($kominfo)->post('/dashboard/desa', ['kecamatan_id' => $kecamatanA->id, 'name' => 'Desa A']);
        $this->actingAs($kominfo)->post('/dashboard/desa', ['kecamatan_id' => $kecamatanB->id, 'name' => 'Desa B']);

        $response = $this->actingAs($kominfo)->get("/dashboard/desa?kecamatan_id={$kecamatanB->id}");

        $response->assertOk();
        $response->assertSee('Desa B');
        $response->assertDontSee('Desa A');
    }
}
