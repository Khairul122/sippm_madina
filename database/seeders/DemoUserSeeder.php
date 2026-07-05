<?php

namespace Database\Seeders;

use App\Infrastructure\Persistence\Eloquent\Models\Kecamatan;
use App\Infrastructure\Persistence\Eloquent\Models\Opd;
use App\Infrastructure\Persistence\Eloquent\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * One demo account per role for manual QA. Email format {role}@demo.test,
 * password "password" for all.
 */
class DemoUserSeeder extends Seeder
{
    public function run(): void
    {
        $firstOpd = Opd::query()->orderBy('id')->first();
        $firstKecamatan = Kecamatan::query()->orderBy('id')->first();

        $roles = [
            'masyarakat' => ['nik' => '1305010101010001'],
            'kominfo' => [],
            'opd' => ['opd_id' => $firstOpd?->id],
            'camat' => ['kecamatan_id' => $firstKecamatan?->id],
            'bupati' => [],
            'wakil_bupati' => [],
            'sekda' => [],
        ];

        foreach ($roles as $roleSlug => $extra) {
            $user = User::firstOrCreate(
                ['email' => "{$roleSlug}@demo.test"],
                array_merge([
                    'name' => ucwords(str_replace('_', ' ', $roleSlug)).' Demo',
                    'password' => Hash::make('password'),
                    'is_active' => true,
                    'email_verified_at' => now(),
                    'consent_at' => now(),
                ], $extra)
            );

            if (! $user->hasRole($roleSlug)) {
                $user->assignRole($roleSlug);
            }
        }
    }
}
