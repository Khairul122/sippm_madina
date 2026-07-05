<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Infrastructure\Persistence\Eloquent\Models\Complaint;
use App\Infrastructure\Persistence\Eloquent\Models\Opd;
use App\Infrastructure\Persistence\Eloquent\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * BR-01/BR-02 end to end via HTTP: a disposition may only ever be routed
 * to an OPD or a Camat — never Bupati/Wakil Bupati/Sekda — enforced both
 * by DisposeComplaintRequest (422 before the UseCase even runs) and by
 * DisposeComplaintUseCase itself.
 */
class ComplaintDispositionTest extends TestCase
{
    use RefreshDatabase;

    private function verifiedComplaint(User $kominfo): Complaint
    {
        $masyarakat = User::query()->where('email', 'masyarakat@demo.test')->firstOrFail();

        $submit = $this->actingAs($masyarakat, 'sanctum')->postJson('/api/v1/complaints', [
            'title' => 'Selokan tersumbat',
            'category' => 'lingkungan',
            'description' => 'Selokan tersumbat menyebabkan banjir kecil.',
            'target_type' => 'opd',
        ]);

        $complaintId = Complaint::query()->where('ticket_number', $submit->json('data.ticket_number'))->firstOrFail()->id;

        $this->actingAs($kominfo, 'sanctum')->postJson("/api/v1/complaints/{$complaintId}/verify", [
            'is_valid' => true,
        ])->assertStatus(200);

        return Complaint::query()->findOrFail($complaintId);
    }

    public function test_disposition_to_bupati_is_rejected_with_422(): void
    {
        $this->seed();
        $kominfo = User::query()->where('email', 'kominfo@demo.test')->firstOrFail();
        $complaint = $this->verifiedComplaint($kominfo);

        $response = $this->actingAs($kominfo, 'sanctum')->postJson("/api/v1/complaints/{$complaint->id}/dispose", [
            'targets' => [
                ['type' => 'bupati', 'id' => 1],
            ],
        ]);

        $response->assertStatus(422);
        $this->assertDatabaseMissing('dispositions', ['complaint_id' => $complaint->id]);
        $this->assertDatabaseHas('complaints', ['id' => $complaint->id, 'status' => 'diverifikasi']);
    }

    public function test_disposition_to_opd_succeeds(): void
    {
        $this->seed();
        $kominfo = User::query()->where('email', 'kominfo@demo.test')->firstOrFail();
        $complaint = $this->verifiedComplaint($kominfo);
        $opd = Opd::query()->firstOrFail();

        $response = $this->actingAs($kominfo, 'sanctum')->postJson("/api/v1/complaints/{$complaint->id}/dispose", [
            'targets' => [
                ['type' => 'opd', 'id' => $opd->id],
            ],
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('dispositions', [
            'complaint_id' => $complaint->id,
            'disposed_to_type' => 'opd',
            'disposed_to_id' => $opd->id,
        ]);
        $this->assertDatabaseHas('complaints', ['id' => $complaint->id, 'status' => 'diproses']);
    }

    /**
     * Regression: the dashboard dispose form (dashboard/complaints/show.blade.php)
     * renders a checkbox per OPD/Kecamatan and only submits an entry for
     * checked boxes — so `targets` can arrive as a sparse, non-zero-based
     * array (e.g. only index 3 present, from the 4th checkbox in the
     * list). DisposeComplaintUseCase used to build its internal
     * `$targetTypes` lookup by re-indexing from 0 while reading it back
     * by the original submitted index, causing an "Undefined array key"
     * error for any selection that wasn't the very first checkbox(es).
     */
    public function test_disposition_with_sparse_non_zero_target_index_succeeds(): void
    {
        $this->seed();
        $kominfo = User::query()->where('email', 'kominfo@demo.test')->firstOrFail();
        $complaint = $this->verifiedComplaint($kominfo);
        $opd = Opd::query()->firstOrFail();

        $response = $this->actingAs($kominfo, 'sanctum')->postJson("/api/v1/complaints/{$complaint->id}/dispose", [
            'targets' => [
                3 => ['type' => 'opd', 'id' => $opd->id],
            ],
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('dispositions', [
            'complaint_id' => $complaint->id,
            'disposed_to_type' => 'opd',
            'disposed_to_id' => $opd->id,
        ]);
        $this->assertDatabaseHas('complaints', ['id' => $complaint->id, 'status' => 'diproses']);
    }
}
