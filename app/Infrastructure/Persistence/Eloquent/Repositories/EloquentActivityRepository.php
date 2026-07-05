<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Activity\Entities\Activity as ActivityEntity;
use App\Domain\Activity\Repositories\ActivityRepositoryInterface;
use App\Domain\Activity\ValueObjects\ActivityStatus;
use App\Infrastructure\Persistence\Eloquent\Models\Activity as ActivityModel;
use DateTimeImmutable;

class EloquentActivityRepository implements ActivityRepositoryInterface
{
    public function save(ActivityEntity $activity): ActivityEntity
    {
        $model = $activity->id
            ? ActivityModel::query()->findOrFail($activity->id)
            : new ActivityModel();

        $model->fill([
            'title' => $activity->title,
            'description' => $activity->description,
            'actor_type' => $activity->actorType,
            'actor_id' => $activity->actorId,
            'date' => $activity->date->format('Y-m-d'),
            'location' => $activity->location,
            'status' => $activity->status,
            'rejection_reason' => $activity->rejectionReason,
        ]);

        $model->save();

        return $this->toEntity($model);
    }

    public function findById(int $id): ?ActivityEntity
    {
        $model = ActivityModel::query()->find($id);

        return $model ? $this->toEntity($model) : null;
    }

    public function updateStatus(int $activityId, ActivityStatus $status, ?string $rejectionReason = null): void
    {
        $model = ActivityModel::query()->findOrFail($activityId);
        $model->status = $status;

        if ($rejectionReason !== null) {
            $model->rejection_reason = $rejectionReason;
        }

        $model->save();
    }

    public function paginate(array $filters, int $page = 1, int $perPage = 15): array
    {
        $query = ActivityModel::query();

        if (! empty($filters['status'])) {
            $status = $filters['status'] instanceof ActivityStatus ? $filters['status']->value : $filters['status'];
            $query->where('status', $status);
        }

        if (! empty($filters['actor_type'])) {
            $query->where('actor_type', $filters['actor_type']);
        }

        if (! empty($filters['actor_id'])) {
            $query->where('actor_id', $filters['actor_id']);
        }

        if (! empty($filters['date_from'])) {
            $query->whereDate('date', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->whereDate('date', '<=', $filters['date_to']);
        }

        $paginator = $query->orderByDesc('date')->paginate($perPage, ['*'], 'page', $page);

        return $this->paginatorToArray($paginator);
    }

    public function paginatePublished(int $page = 1, int $perPage = 15): array
    {
        $paginator = ActivityModel::query()
            ->where('status', ActivityStatus::DIPUBLIKASIKAN->value)
            ->orderByDesc('date')
            ->paginate($perPage, ['*'], 'page', $page);

        return $this->paginatorToArray($paginator);
    }

    public function delete(int $activityId): void
    {
        ActivityModel::query()->findOrFail($activityId)->delete();
    }

    private function paginatorToArray($paginator): array
    {
        return [
            'data' => array_map(fn (ActivityModel $m) => $this->toEntity($m), $paginator->items()),
            'total' => $paginator->total(),
            'per_page' => $paginator->perPage(),
            'current_page' => $paginator->currentPage(),
        ];
    }

    private function toEntity(ActivityModel $model): ActivityEntity
    {
        return new ActivityEntity(
            id: $model->id,
            title: $model->title,
            description: $model->description,
            actorType: $model->actor_type,
            actorId: $model->actor_id,
            date: DateTimeImmutable::createFromFormat('Y-m-d', $model->date->format('Y-m-d')),
            location: $model->location,
            status: $model->status instanceof ActivityStatus ? $model->status : ActivityStatus::from($model->status),
            rejectionReason: $model->rejection_reason,
            createdAt: $model->created_at ? DateTimeImmutable::createFromInterface($model->created_at) : null,
            updatedAt: $model->updated_at ? DateTimeImmutable::createFromInterface($model->updated_at) : null,
            deletedAt: $model->deleted_at ? DateTimeImmutable::createFromInterface($model->deleted_at) : null,
        );
    }
}
