<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Complaint\Repositories\ComplaintStatusHistoryRepositoryInterface;
use App\Domain\Complaint\ValueObjects\ComplaintStatus;
use App\Infrastructure\Persistence\Eloquent\Models\ComplaintStatusHistory;

class EloquentComplaintStatusHistoryRepository implements ComplaintStatusHistoryRepositoryInterface
{
    public function recordChange(
        int $complaintId,
        ComplaintStatus $status,
        ?string $note,
        int $changedByUserId,
    ): void {
        ComplaintStatusHistory::query()->create([
            'complaint_id' => $complaintId,
            'status' => $status,
            'note' => $note,
            'changed_by' => $changedByUserId,
        ]);
    }

    public function listForComplaint(int $complaintId): array
    {
        return ComplaintStatusHistory::query()
            ->where('complaint_id', $complaintId)
            ->orderBy('created_at')
            ->get()
            ->map(fn (ComplaintStatusHistory $h) => [
                'status' => $h->status instanceof ComplaintStatus ? $h->status : ComplaintStatus::from($h->status),
                'note' => $h->note,
                'changed_by' => $h->changed_by,
                'created_at' => (string) $h->created_at,
            ])
            ->all();
    }
}
