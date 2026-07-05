<?php

declare(strict_types=1);

namespace App\Domain\Activity\Repositories;

use App\Domain\Activity\Entities\Activity;
use App\Domain\Activity\ValueObjects\ActivityStatus;

interface ActivityRepositoryInterface
{
    public function save(Activity $activity): Activity;

    public function findById(int $id): ?Activity;

    public function updateStatus(int $activityId, ActivityStatus $status, ?string $rejectionReason = null): void;

    /**
     * @param array{
     *     status?: ActivityStatus|string,
     *     actor_type?: string,
     *     actor_id?: int,
     *     date_from?: string,
     *     date_to?: string,
     * } $filters
     * @return array{data: Activity[], total: int, per_page: int, current_page: int}
     */
    public function paginate(array $filters, int $page = 1, int $perPage = 15): array;

    /**
     * Publicly published activities feed (status = DIPUBLIKASIKAN only).
     *
     * @return array{data: Activity[], total: int, per_page: int, current_page: int}
     */
    public function paginatePublished(int $page = 1, int $perPage = 15): array;

    public function delete(int $activityId): void;
}
