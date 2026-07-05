<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Complaint\Entities\Complaint as ComplaintEntity;
use App\Domain\Complaint\Repositories\ComplaintRepositoryInterface;
use App\Domain\Complaint\ValueObjects\ComplaintStatus;
use App\Domain\Complaint\ValueObjects\TargetType;
use App\Domain\Complaint\ValueObjects\TicketNumber;
use App\Infrastructure\Persistence\Eloquent\Models\Complaint as ComplaintModel;
use DateTimeImmutable;

class EloquentComplaintRepository implements ComplaintRepositoryInterface
{
    public function save(ComplaintEntity $complaint): ComplaintEntity
    {
        $model = $complaint->id
            ? ComplaintModel::query()->findOrFail($complaint->id)
            : new ComplaintModel();

        $model->fill([
            'ticket_number' => (string) $complaint->ticketNumber,
            'user_id' => $complaint->userId,
            'title' => $complaint->title,
            'description' => $complaint->description,
            'category' => $complaint->category,
            'target_type' => $complaint->targetType->value,
            'target_id' => $complaint->targetId,
            'status' => $complaint->status,
            'latitude' => $complaint->latitude,
            'longitude' => $complaint->longitude,
            'rejection_reason' => $complaint->rejectionReason,
        ]);

        $model->save();

        return $this->toEntity($model);
    }

    public function findById(int $id): ?ComplaintEntity
    {
        $model = ComplaintModel::query()->find($id);

        return $model ? $this->toEntity($model) : null;
    }

    public function findByTicketNumber(string $ticketNumber): ?ComplaintEntity
    {
        $model = ComplaintModel::query()->where('ticket_number', $ticketNumber)->first();

        return $model ? $this->toEntity($model) : null;
    }

    public function countForYear(int $year): int
    {
        return ComplaintModel::query()
            ->where('ticket_number', 'like', "PGD-{$year}-%")
            ->withTrashed()
            ->count();
    }

    public function updateStatus(int $complaintId, ComplaintStatus $status, ?string $rejectionReason = null): void
    {
        $model = ComplaintModel::query()->findOrFail($complaintId);
        $model->status = $status;

        if ($rejectionReason !== null) {
            $model->rejection_reason = $rejectionReason;
        }

        $model->save();
    }

    public function paginate(array $filters, int $page = 1, int $perPage = 15): array
    {
        $query = ComplaintModel::query();

        if (! empty($filters['status'])) {
            $status = $filters['status'] instanceof ComplaintStatus ? $filters['status']->value : $filters['status'];
            $query->where('status', $status);
        }

        if (! empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        if (! empty($filters['target_type'])) {
            $targetType = $filters['target_type'] instanceof TargetType
                ? $filters['target_type']->value
                : $filters['target_type'];
            $query->where('target_type', $targetType);
        }

        if (! empty($filters['target_id'])) {
            $query->where('target_id', $filters['target_id']);
        }

        if (! empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (! empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        $paginator = $query->orderByDesc('created_at')->paginate($perPage, ['*'], 'page', $page);

        return [
            'data' => array_map(fn (ComplaintModel $m) => $this->toEntity($m), $paginator->items()),
            'total' => $paginator->total(),
            'per_page' => $paginator->perPage(),
            'current_page' => $paginator->currentPage(),
        ];
    }

    public function delete(int $complaintId): void
    {
        ComplaintModel::query()->findOrFail($complaintId)->delete();
    }

    private function toEntity(ComplaintModel $model): ComplaintEntity
    {
        return new ComplaintEntity(
            id: $model->id,
            ticketNumber: TicketNumber::fromString($model->ticket_number),
            userId: $model->user_id,
            title: $model->title,
            description: $model->description,
            category: $model->category,
            targetType: TargetType::from($model->target_type),
            targetId: $model->target_id,
            status: $model->status instanceof ComplaintStatus ? $model->status : ComplaintStatus::from($model->status),
            latitude: $model->latitude !== null ? (float) $model->latitude : null,
            longitude: $model->longitude !== null ? (float) $model->longitude : null,
            rejectionReason: $model->rejection_reason,
            createdAt: $model->created_at ? DateTimeImmutable::createFromInterface($model->created_at) : null,
            updatedAt: $model->updated_at ? DateTimeImmutable::createFromInterface($model->updated_at) : null,
            deletedAt: $model->deleted_at ? DateTimeImmutable::createFromInterface($model->deleted_at) : null,
        );
    }
}
