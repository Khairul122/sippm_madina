<?php

declare(strict_types=1);

namespace App\Application\UseCases\Activity;

use App\Domain\Activity\Entities\Activity;
use App\Domain\Activity\Repositories\ActivityRepositoryInterface;
use App\Domain\Activity\ValueObjects\ActivityStatus;
use DomainException;
use InvalidArgumentException;

/**
 * Kominfo menarik kembali kegiatan yang sudah dipublikasikan ke feed publik
 * (-> DRAFT), misalnya untuk memperbaiki data sebelum dipublikasikan ulang.
 * Hanya kegiatan berstatus DIPUBLIKASIKAN yang dapat ditarik.
 */
final class UnpublishActivityUseCase
{
    public function __construct(
        private readonly ActivityRepositoryInterface $activities,
    ) {
    }

    public function execute(int $activityId): Activity
    {
        $activity = $this->activities->findById($activityId);

        if ($activity === null) {
            throw new InvalidArgumentException("Kegiatan #{$activityId} tidak ditemukan.");
        }

        if ($activity->status !== ActivityStatus::DIPUBLIKASIKAN) {
            throw new DomainException('Hanya kegiatan yang sudah dipublikasikan yang dapat ditarik kembali ke draft.');
        }

        $this->activities->updateStatus($activity->id, ActivityStatus::DRAFT);

        return $activity->withStatus(ActivityStatus::DRAFT);
    }
}
