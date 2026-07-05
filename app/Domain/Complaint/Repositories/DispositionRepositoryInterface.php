<?php

declare(strict_types=1);

namespace App\Domain\Complaint\Repositories;

use App\Domain\Complaint\ValueObjects\TargetType;

interface DispositionRepositoryInterface
{
    /**
     * Create a disposition row routing a complaint to an OPD/Camat.
     * Callers MUST have already validated the target via
     * DispositionMustTargetOpdOrCamatRule::assert() before calling this.
     *
     * @return int the new disposition id
     */
    public function create(
        int $complaintId,
        TargetType $disposedToType,
        int $disposedToId,
        int $disposedByUserId,
        ?string $note,
    ): int;

    /**
     * @return array<int, array{
     *     id: int,
     *     complaint_id: int,
     *     disposed_to_type: TargetType,
     *     disposed_to_id: int,
     *     disposed_by: int,
     *     note: ?string,
     *     created_at: string,
     * }>
     */
    public function listForComplaint(int $complaintId): array;

    public function findById(int $id): ?array;
}
