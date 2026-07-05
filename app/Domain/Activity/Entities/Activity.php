<?php

declare(strict_types=1);

namespace App\Domain\Activity\Entities;

use App\Domain\Activity\ValueObjects\ActivityStatus;
use DateTimeImmutable;

/**
 * Plain PHP representation of an activity (kegiatan) report, mirroring the
 * `activities` table. `actorType`/`actorId` is a polymorphic reference to
 * the reporting OPD or Kecamatan (mapped to Eloquent morphTo in
 * Infrastructure).
 */
final class Activity
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $title,
        public readonly string $description,
        public readonly string $actorType,
        public readonly int $actorId,
        public readonly DateTimeImmutable $date,
        public readonly ?string $location,
        public readonly ActivityStatus $status,
        public readonly ?string $rejectionReason = null,
        public readonly ?DateTimeImmutable $createdAt = null,
        public readonly ?DateTimeImmutable $updatedAt = null,
        public readonly ?DateTimeImmutable $deletedAt = null,
    ) {
    }

    public function withStatus(ActivityStatus $status, ?string $rejectionReason = null): self
    {
        return new self(
            id: $this->id,
            title: $this->title,
            description: $this->description,
            actorType: $this->actorType,
            actorId: $this->actorId,
            date: $this->date,
            location: $this->location,
            status: $status,
            rejectionReason: $rejectionReason ?? $this->rejectionReason,
            createdAt: $this->createdAt,
            updatedAt: $this->updatedAt,
            deletedAt: $this->deletedAt,
        );
    }
}
