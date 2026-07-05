<?php

declare(strict_types=1);

namespace App\Domain\Notification\Repositories;

use App\Domain\Notification\Entities\NotificationMessage;

interface NotificationRepositoryInterface
{
    public function create(NotificationMessage $notification): NotificationMessage;

    public function findById(int $id): ?NotificationMessage;

    public function markAsRead(int $id): void;

    public function countUnreadForUser(int $userId): int;

    /**
     * @return array{data: NotificationMessage[], total: int, per_page: int, current_page: int}
     */
    public function paginateForUser(int $userId, int $page = 1, int $perPage = 15): array;
}
