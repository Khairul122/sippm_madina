<?php

declare(strict_types=1);

namespace App\Application\UseCases\Activity;

use App\Application\DTOs\SubmitActivityDTO;
use App\Domain\Activity\Entities\Activity;
use App\Domain\Activity\Repositories\ActivityRepositoryInterface;
use App\Domain\Activity\ValueObjects\ActivityStatus;
use App\Infrastructure\Persistence\Eloquent\Models\ActivityDocumentation;
use DateTimeImmutable;
use Illuminate\Support\Facades\DB;

/**
 * OPD/Camat reports a kegiatan (activity). Initial status DRAFT — must be
 * verified (BR-08, VerifyActivityUseCase) before it can be published.
 */
final class SubmitActivityUseCase
{
    public function __construct(
        private readonly ActivityRepositoryInterface $activities,
    ) {
    }

    public function execute(SubmitActivityDTO $dto): Activity
    {
        return DB::transaction(function () use ($dto): Activity {
            $activity = new Activity(
                id: null,
                title: $dto->title,
                description: $dto->description,
                actorType: $dto->actorType,
                actorId: $dto->actorId,
                date: new DateTimeImmutable($dto->date),
                location: $dto->location,
                status: ActivityStatus::DRAFT,
            );

            $saved = $this->activities->save($activity);

            foreach ($dto->documentationPaths as $path) {
                ActivityDocumentation::query()->create([
                    'activity_id' => $saved->id,
                    'file_path' => $path,
                    'caption' => null,
                ]);
            }

            return $saved;
        });
    }
}
