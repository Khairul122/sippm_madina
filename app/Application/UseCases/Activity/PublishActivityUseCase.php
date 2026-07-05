<?php

declare(strict_types=1);

namespace App\Application\UseCases\Activity;

use App\Domain\Activity\Entities\Activity;
use App\Domain\Activity\Repositories\ActivityRepositoryInterface;
use App\Domain\Activity\ValueObjects\ActivityStatus;
use App\Infrastructure\Broadcasting\Events\ActivityPublished;
use DomainException;
use InvalidArgumentException;

/**
 * BR-08: Kominfo publishes a verified activity report (-> DIPUBLIKASIKAN).
 * Only a DIVERIFIKASI activity may be published — never straight from DRAFT.
 */
final class PublishActivityUseCase
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

        if ($activity->status !== ActivityStatus::DIVERIFIKASI) {
            throw new DomainException('Hanya kegiatan yang sudah diverifikasi yang dapat dipublikasikan.');
        }

        $this->activities->updateStatus($activity->id, ActivityStatus::DIPUBLIKASIKAN);

        $published = $activity->withStatus(ActivityStatus::DIPUBLIKASIKAN);

        event(new ActivityPublished($published));

        return $published;
    }
}
