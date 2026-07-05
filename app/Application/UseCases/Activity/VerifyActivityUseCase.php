<?php

declare(strict_types=1);

namespace App\Application\UseCases\Activity;

use App\Application\DTOs\VerifyActivityDTO;
use App\Domain\Activity\Entities\Activity;
use App\Domain\Activity\Repositories\ActivityRepositoryInterface;
use App\Domain\Activity\ValueObjects\ActivityStatus;
use DomainException;
use InvalidArgumentException;

/**
 * BR-08: Kominfo must verify an activity report as valid (-> DIVERIFIKASI)
 * or invalid (-> DITOLAK, rejection reason mandatory) before it may ever be
 * published. Only a DRAFT activity may be verified.
 */
final class VerifyActivityUseCase
{
    public function __construct(
        private readonly ActivityRepositoryInterface $activities,
    ) {
    }

    public function execute(VerifyActivityDTO $dto): Activity
    {
        $activity = $this->activities->findById($dto->activityId);

        if ($activity === null) {
            throw new InvalidArgumentException("Kegiatan #{$dto->activityId} tidak ditemukan.");
        }

        if ($activity->status !== ActivityStatus::DRAFT) {
            throw new DomainException('Hanya kegiatan berstatus draft yang dapat diverifikasi.');
        }

        if (! $dto->isValid && empty($dto->rejectionReason)) {
            throw new DomainException('Alasan penolakan wajib diisi ketika kegiatan dinyatakan tidak valid.');
        }

        $targetStatus = $dto->isValid ? ActivityStatus::DIVERIFIKASI : ActivityStatus::DITOLAK;

        $this->activities->updateStatus($activity->id, $targetStatus, $dto->rejectionReason);

        return $activity->withStatus($targetStatus, $dto->rejectionReason);
    }
}
