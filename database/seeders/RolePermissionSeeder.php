<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Seeds the 7 SIPPM Madina roles and the permission matrix from PRD 4.2
 * (14 features x 7 roles).
 */
class RolePermissionSeeder extends Seeder
{
    private const ROLES = [
        'masyarakat',
        'kominfo',
        'opd',
        'camat',
        'bupati',
        'wakil_bupati',
        'sekda',
    ];

    /**
     * @var array<string, string[]> permission slug => role slugs allowed
     */
    private const MATRIX = [
        'registrasi_login' => [
            'masyarakat', 'kominfo', 'opd', 'camat', 'bupati', 'wakil_bupati', 'sekda',
        ],
        'buat_pengaduan' => ['masyarakat'],
        'verifikasi_pengaduan' => ['kominfo'],
        'disposisi_pengaduan' => ['kominfo'],
        'menangani_pengaduan' => ['opd', 'camat'],
        'kirim_hasil_penanganan' => ['opd', 'camat'],
        'menjawab_masyarakat' => ['kominfo'],
        'input_kegiatan' => ['opd', 'camat'],
        'verifikasi_publikasi_kegiatan' => ['kominfo'],
        'melihat_statistik' => ['kominfo', 'opd', 'camat', 'bupati', 'wakil_bupati', 'sekda'],
        'monitoring_kinerja' => ['kominfo', 'bupati', 'wakil_bupati', 'sekda'],
        'lihat_laporan_kegiatan' => ['kominfo', 'opd', 'camat', 'bupati', 'wakil_bupati', 'sekda'],
        'kelola_pengguna' => ['kominfo'],
        'lihat_audit_log' => ['kominfo'],
    ];

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $roles = [];
        foreach (self::ROLES as $roleSlug) {
            $roles[$roleSlug] = Role::firstOrCreate(['name' => $roleSlug, 'guard_name' => 'web']);
        }

        foreach (self::MATRIX as $permissionSlug => $allowedRoles) {
            $permission = Permission::firstOrCreate(['name' => $permissionSlug, 'guard_name' => 'web']);

            foreach ($allowedRoles as $roleSlug) {
                $roles[$roleSlug]->givePermissionTo($permission);
            }
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
