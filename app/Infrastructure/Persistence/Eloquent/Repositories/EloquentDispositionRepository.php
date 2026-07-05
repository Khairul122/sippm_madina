<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Complaint\Repositories\DispositionRepositoryInterface;
use App\Domain\Complaint\ValueObjects\TargetType;
use App\Infrastructure\Persistence\Eloquent\Models\Disposition;

class EloquentDispositionRepository implements DispositionRepositoryInterface
{
    public function create(
        int $complaintId,
        TargetType $disposedToType,
        int $disposedToId,
        int $disposedByUserId,
        ?string $note,
    ): int {
        $disposition = Disposition::query()->create([
            'complaint_id' => $complaintId,
            'disposed_to_type' => $disposedToType->value,
            'disposed_to_id' => $disposedToId,
            'disposed_by' => $disposedByUserId,
            'note' => $note,
        ]);

        return $disposition->id;
    }

    public function listForComplaint(int $complaintId): array
    {
        return Disposition::query()
            ->where('complaint_id', $complaintId)
            ->orderBy('created_at')
            ->get()
            ->map(fn (Disposition $d) => $this->toArray($d))
            ->all();
    }

    public function findById(int $id): ?array
    {
        $disposition = Disposition::query()->find($id);

        return $disposition ? $this->toArray($disposition) : null;
    }

    private function toArray(Disposition $d): array
    {
        return [
            'id' => $d->id,
            'complaint_id' => $d->complaint_id,
            'disposed_to_type' => TargetType::from($d->disposed_to_type),
            'disposed_to_id' => $d->disposed_to_id,
            'disposed_by' => $d->disposed_by,
            'note' => $d->note,
            'created_at' => (string) $d->created_at,
        ];
    }
}
