<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\ApiResponds;
use App\Http\Controllers\Controller;
use App\Http\Resources\ComplaintPublicResource;
use App\Infrastructure\Persistence\Eloquent\Models\Complaint;
use Illuminate\Http\JsonResponse;

/**
 * NO AUTH — public ticket tracking. Only ComplaintPublicResource fields are
 * ever returned (no internal id / user_id / disposition data).
 */
class PublicComplaintController extends Controller
{
    use ApiResponds;

    public function track(string $ticketNumber): JsonResponse
    {
        $complaint = Complaint::query()
            ->with('statusHistories')
            ->where('ticket_number', $ticketNumber)
            ->first();

        if (! $complaint) {
            return $this->error('Nomor tiket tidak ditemukan.', 404);
        }

        return $this->success(new ComplaintPublicResource($complaint));
    }
}
