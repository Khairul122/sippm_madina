<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Notification\Entities\NotificationMessage;
use App\Domain\Notification\Repositories\NotificationRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Models\Notification as NotificationModel;
use DateTimeImmutable;

class EloquentNotificationRepository implements NotificationRepositoryInterface
{
    public function create(NotificationMessage $notification): NotificationMessage
    {
        $model = NotificationModel::query()->create([
            'user_id' => $notification->userId,
            'title' => $notification->title,
            'message' => $notification->message,
            'type' => $notification->type,
            'is_read' => $notification->isRead,
            'data' => $notification->data,
        ]);

        return $this->toEntity($model);
    }

    public function findById(int $id): ?NotificationMessage
    {
        $model = NotificationModel::query()->find($id);

        return $model ? $this->toEntity($model) : null;
    }

    public function markAsRead(int $id): void
    {
        NotificationModel::query()->findOrFail($id)->update(['is_read' => true]);
    }

    public function countUnreadForUser(int $userId): int
    {
        return NotificationModel::query()
            ->where('user_id', $userId)
            ->where('is_read', false)
            ->count();
    }

    public function paginateForUser(int $userId, int $page = 1, int $perPage = 15): array
    {
        $paginator = NotificationModel::query()
            ->where('user_id', $userId)
            ->orderByDesc('created_at')
            ->paginate($perPage, ['*'], 'page', $page);

        return [
            'data' => array_map(fn (NotificationModel $m) => $this->toEntity($m), $paginator->items()),
            'total' => $paginator->total(),
            'per_page' => $paginator->perPage(),
            'current_page' => $paginator->currentPage(),
        ];
    }

    private function toEntity(NotificationModel $model): NotificationMessage
    {
        return new NotificationMessage(
            id: $model->id,
            userId: $model->user_id,
            title: $model->title,
            message: $model->message,
            type: $model->type,
            isRead: (bool) $model->is_read,
            data: $model->data,
            createdAt: $model->created_at ? DateTimeImmutable::createFromInterface($model->created_at) : null,
            updatedAt: $model->updated_at ? DateTimeImmutable::createFromInterface($model->updated_at) : null,
        );
    }
}
