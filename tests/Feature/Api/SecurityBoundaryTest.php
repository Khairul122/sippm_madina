<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Infrastructure\Persistence\Eloquent\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityBoundaryTest extends TestCase
{
    use RefreshDatabase;

    public function test_citizen_cannot_read_another_citizens_complaint_or_history(): void
    {
        $this->seed();
        $owner = User::query()->where('email', 'masyarakat@gmail.com')->firstOrFail();
        $other = User::query()->create([
            'name' => 'Warga lain',
            'email' => 'warga-lain@example.test',
            'password' => 'safe-test-password',
            'nik' => '1305010101010099',
            'phone' => '081234567899',
            'is_active' => true,
        ]);
        $other->assignRole('masyarakat');

        $created = $this->actingAs($owner, 'sanctum')->postJson('/api/v1/complaints', [
            'title' => 'Jalan rusak',
            'category' => 'infrastruktur',
            'description' => '<p>Jalan berlubang.</p>',
            'target_type' => 'opd',
        ])->assertCreated();
        $id = $created->json('data.id');

        $this->actingAs($other, 'sanctum')->getJson("/api/v1/complaints/{$id}")->assertForbidden();
        $this->getJson("/api/v1/complaints/{$id}/history")->assertForbidden();
        $this->getJson('/api/v1/complaints')->assertJsonMissing(['id' => $id]);
    }

    public function test_inactive_account_cannot_use_existing_api_session(): void
    {
        $this->seed();
        $user = User::query()->where('email', 'masyarakat@gmail.com')->firstOrFail();
        $user->update(['is_active' => false]);

        $this->actingAs($user, 'sanctum')->getJson('/api/v1/me')->assertForbidden();
    }

    public function test_maintenance_http_routes_are_absent(): void
    {
        foreach (['/sys/clear', '/sys/link', '/sys/migrate', '/sys/seed', '/api/v1/sys/migrate', '/sys-unzip.php'] as $path) {
            $this->get($path)->assertNotFound();
        }
    }

    public function test_production_seeding_does_not_create_default_accounts(): void
    {
        app()->detectEnvironment(fn () => 'production');

        $this->artisan('db:seed', ['--force' => true])->run();

        $this->assertDatabaseCount('users', 0);
    }
}
