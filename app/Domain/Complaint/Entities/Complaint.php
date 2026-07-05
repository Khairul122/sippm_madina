<?php

declare(strict_types=1);

namespace App\Domain\Complaint\Entities;

use App\Domain\Complaint\ValueObjects\ComplaintStatus;
use App\Domain\Complaint\ValueObjects\TargetType;
use App\Domain\Complaint\ValueObjects\TicketNumber;
use DateTimeImmutable;

/**
 * Plain PHP representation of a complaint (pengaduan), mirroring the
 * `complaints` table columns. This is a Domain entity — it has no
 * persistence knowledge; Infrastructure Eloquent models are separate and
 * are mapped to/from this entity by the repository implementations.
 */
final class Complaint
{
    public function __construct(
        public readonly ?int $id,
        public readonly TicketNumber $ticketNumber,
        public readonly int $userId,
        public readonly string $title,
        public readonly string $description,
        public readonly string $category,
        public readonly TargetType $targetType,
        public readonly ?int $targetId,
        public readonly ComplaintStatus $status,
        public readonly ?float $latitude = null,
        public readonly ?float $longitude = null,
        public readonly ?string $rejectionReason = null,
        public readonly ?DateTimeImmutable $createdAt = null,
        public readonly ?DateTimeImmutable $updatedAt = null,
        public readonly ?DateTimeImmutable $deletedAt = null,
    ) {
    }

    public function withStatus(ComplaintStatus $status, ?string $rejectionReason = null): self
    {
        return new self(
            id: $this->id,
            ticketNumber: $this->ticketNumber,
            userId: $this->userId,
            title: $this->title,
            description: $this->description,
            category: $this->category,
            targetType: $this->targetType,
            targetId: $this->targetId,
            status: $status,
            latitude: $this->latitude,
            longitude: $this->longitude,
            rejectionReason: $rejectionReason ?? $this->rejectionReason,
            createdAt: $this->createdAt,
            updatedAt: $this->updatedAt,
            deletedAt: $this->deletedAt,
        );
    }
}
