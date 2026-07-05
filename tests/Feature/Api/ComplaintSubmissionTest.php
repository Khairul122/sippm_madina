<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Infrastructure\Persistence\Eloquent\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ComplaintSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_masyarakat_can_submit_a_complaint_and_receives_a_valid_ticket_number(): void
    {
        $this->seed();

        $user = User::query()->where('email', 'masyarakat@demo.test')->firstOrFail();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/complaints', [
            'title' => 'Jalan rusak parah',
            'category' => 'infrastruktur',
            'description' => 'Jalan di depan rumah berlubang besar dan membahayakan pengendara.',
            'target_type' => 'opd',
            'target_id' => null,
        ]);

        $response->assertStatus(201);
        $response->assertJsonPath('success', true);

        $ticketNumber = $response->json('data.ticket_number');
        $this->assertMatchesRegularExpression('/^PGD-\d{4}-\d{6}$/', $ticketNumber);
        $this->assertSame('diajukan', $response->json('data.status'));

        $this->assertDatabaseHas('complaints', [
            'ticket_number' => $ticketNumber,
            'user_id' => $user->id,
            'status' => 'diajukan',
        ]);

        $this->assertDatabaseHas('complaint_status_histories', [
            'status' => 'diajukan',
        ]);
    }

    public function test_a_non_masyarakat_role_cannot_submit_a_complaint(): void
    {
        $this->seed();

        $kominfo = User::query()->where('email', 'kominfo@demo.test')->firstOrFail();

        $response = $this->actingAs($kominfo, 'sanctum')->postJson('/api/v1/complaints', [
            'title' => 'Test',
            'category' => 'lainnya',
            'description' => 'Deskripsi test.',
            'target_type' => 'opd',
        ]);

        $response->assertStatus(403);
    }

    public function test_public_tracking_returns_ticket_without_internal_ids(): void
    {
        $this->seed();

        $user = User::query()->where('email', 'masyarakat@demo.test')->firstOrFail();

        $submit = $this->actingAs($user, 'sanctum')->postJson('/api/v1/complaints', [
            'title' => 'Lampu jalan mati',
            'category' => 'infrastruktur',
            'description' => 'Lampu jalan di simpang tiga mati sejak seminggu lalu.',
            'target_type' => 'camat',
        ]);

        $ticketNumber = $submit->json('data.ticket_number');

        $response = $this->getJson("/api/v1/track/{$ticketNumber}");

        $response->assertStatus(200);
        $response->assertJsonMissingPath('data.id');
        $response->assertJsonMissingPath('data.user_id');
        $response->assertJsonPath('data.ticket_number', $ticketNumber);
    }
}
