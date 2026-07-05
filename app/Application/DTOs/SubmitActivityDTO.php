<?php

declare(strict_types=1);

namespace App\Application\DTOs;

/**
 * Input for App\Application\UseCases\Activity\SubmitActivityUseCase.
 * OPD/Camat reports a kegiatan (activity), initial status DRAFT.
 */
final class SubmitActivityDTO
{
    /**
     * @param string[] $documentationPaths
     */
    public function __construct(
        public readonly string $title,
        public readonly string $description,
        public readonly string $actorType,
        public readonly int $actorId,
        public readonly string $date,
        public readonly ?string $location,
        public readonly array $documentationPaths = [],
    ) {
    }
}
