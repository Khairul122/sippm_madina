<?php

namespace Database\Seeders;

use App\Infrastructure\Persistence\Eloquent\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Single Kominfo admin account (admin utama sistem, per PRD bagian 4.1).
 * Terpisah dari DemoUserSeeder — seeder ini untuk akun admin nyata
 * pertama kali sistem dipasang, bukan akun QA per-role.
 *
 * PENTING: ganti password default ini segera setelah login pertama.
 */
class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Administrator Kominfo',
                'password' => Hash::make('admin12345'),
                'is_active' => true,
                'email_verified_at' => now(),
                'consent_at' => now(),
            ]
        );

        if (! $user->hasRole('kominfo')) {
            $user->assignRole('kominfo');
        }
    }
}
