<?php

namespace Database\Seeders;

use App\Infrastructure\Persistence\Eloquent\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Local/test Kominfo admin account. Production provisioning uses a
 * unique password outside source control.
 */
class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->isProduction()) {
            return;
        }

        $user = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Administrator Kominfo',
                'password' => Hash::make('sipapa12345678'),
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
