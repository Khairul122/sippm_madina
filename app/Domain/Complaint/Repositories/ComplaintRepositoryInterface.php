<?php

declare(strict_types=1);

namespace App\Domain\Complaint\Repositories;

use App\Domain\Complaint\Entities\Complaint;
use App\Domain\Complaint\ValueObjects\ComplaintStatus;
use App\Domain\Complaint\ValueObjects\TargetType;

/**
 * Persistence contract for Complaint aggregates.
 *
 * Implemented by Infrastructure\Persistence\Eloquent\Repositories\EloquentComplaintRepository.
 * Consumed by Application-layer UseCases (Fase 4) — Domain itself never
 * talks to the database directly.
 */
interface ComplaintRepositoryInterface
{
    /**
     * Persist a new or existing Complaint and return it with its id set.
     */
    public function save(Complaint $complaint): Complaint;

    public function findById(int $id): ?Complaint;

    public function findByTicketNumber(string $ticketNumber): ?Complaint;

    /**
     * Number of complaints already created in the given year — used by
     * TicketNumberGeneratorRule (via the UseCase) to derive the next
     * sequence number for that year.
     */
    public function countForYear(int $year): int;

    /**
     * Update just the status (+ optional rejection reason) of a complaint.
     * The UseCase is responsible for validating the transition via
     * StatusTransitionGuard before calling this.
     */
    public function updateStatus(int $complaintId, ComplaintStatus $status, ?string $rejectionReason = null): void;

    /**
     * Filtered, paginated listing.
     *
     * @param array{
     *     status?: ComplaintStatus|string,
     *     category?: string,
     *     target_type?: TargetType|string,
     *     target_id?: int,
     *     user_id?: int,
     *     date_from?: string,
     *     date_to?: string,
     * } $filters
     * @return array{data: Complaint[], total: int, per_page: int, current_page: int}
     */
    public function paginate(array $filters, int $page = 1, int $perPage = 15): array;

    public function delete(int $complaintId): void;
}
