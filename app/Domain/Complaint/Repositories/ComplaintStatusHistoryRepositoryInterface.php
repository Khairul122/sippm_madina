<?php

declare(strict_types=1);

namespace App\Domain\Complaint\Repositories;

use App\Domain\Complaint\ValueObjects\ComplaintStatus;

interface ComplaintStatusHistoryRepositoryInterface
{
    /**
     * Append an immutable status-change record for a complaint.
     */
    public function recordChange(
        int $complaintId,
        ComplaintStatus $status,
        ?string $note,
        int $changedByUserId,
    ): void;

    /**
     * @return array<int, array{status: ComplaintStatus, note: ?string, changed_by: int, created_at: string}>
     */
    public function listForComplaint(int $complaintId): array;
}
