<?php

declare(strict_types=1);

namespace Tests\Feature\Web;

use App\Infrastructure\Persistence\Eloquent\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_role_can_view_own_profile(): void
    {
        $this->seed();

        foreach (['masyarakat', 'kominfo', 'opd', 'camat', 'bupati', 'wakil_bupati', 'sekda'] as $role) {
            $user = User::query()->where('email', "{$role}@demo.test")->firstOrFail();
            $this->actingAs($user)->get('/profil')->assertOk();
        }
    }

    public function test_user_can_update_name_and_phone(): void
    {
        $this->seed();

        $user = User::query()->where('email', 'kominfo@demo.test')->firstOrFail();

        $this->actingAs($user)->put('/profil', [
            'name' => 'Nama Baru',
            'phone' => '081200000099',
        ])->assertRedirect();

        $fresh = $user->fresh();
        $this->assertSame('Nama Baru', $fresh->name);
        $this->assertSame('081200000099', $fresh->phone);
    }

    public function test_profile_update_only_ever_touches_the_authenticated_user(): void
    {
        $this->seed();

        $userA = User::query()->where('email', 'opd@demo.test')->firstOrFail();
        $userB = User::query()->where('email', 'camat@demo.test')->firstOrFail();
        $originalNameB = $userB->name;

        $this->actingAs($userA)->put('/profil', ['name' => 'Diubah A'])->assertRedirect();

        $this->assertSame('Diubah A', $userA->fresh()->name);
        $this->assertSame($originalNameB, $userB->fresh()->name);
    }

    public function test_avatar_upload_stores_file_and_updates_avatar_path(): void
    {
        Storage::fake('public');
        $this->seed();

        $user = User::query()->where('email', 'kominfo@demo.test')->firstOrFail();

        $this->actingAs($user)->post('/profil/avatar', [
            'avatar' => UploadedFile::fake()->image('photo.jpg'),
        ])->assertRedirect();

        $fresh = $user->fresh();
        $this->assertNotNull($fresh->avatar_path);
        Storage::disk('public')->assertExists($fresh->avatar_path);
    }

    public function test_avatar_replace_deletes_old_file(): void
    {
        Storage::fake('public');
        $this->seed();

        $user = User::query()->where('email', 'kominfo@demo.test')->firstOrFail();

        $this->actingAs($user)->post('/profil/avatar', [
            'avatar' => UploadedFile::fake()->image('first.jpg'),
        ]);
        $oldPath = $user->fresh()->avatar_path;

        $this->actingAs($user)->post('/profil/avatar', [
            'avatar' => UploadedFile::fake()->image('second.jpg'),
        ]);
        $newPath = $user->fresh()->avatar_path;

        $this->assertNotSame($oldPath, $newPath);
        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($newPath);
    }

    public function test_password_change_requires_correct_current_password(): void
    {
        $this->seed();

        $user = User::query()->where('email', 'kominfo@demo.test')->firstOrFail();

        $this->actingAs($user)->put('/profil/password', [
            'current_password' => 'salah-password',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ])->assertSessionHasErrors('current_password');
    }

    public function test_password_change_updates_hash_with_correct_current_password(): void
    {
        $this->seed();

        $user = User::query()->where('email', 'kominfo@demo.test')->firstOrFail();

        $this->actingAs($user)->put('/profil/password', [
            'current_password' => 'password',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ])->assertRedirect();

        $this->assertTrue(Hash::check('newpassword123', $user->fresh()->password));
    }
}
