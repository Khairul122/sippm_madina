<?php

declare(strict_types=1);

namespace App\Application\DTOs;

/**
 * Input for App\Application\UseCases\Activity\VerifyActivityUseCase (BR-08 —
 * an activity must be verified before it can be published).
 */
final class VerifyActivityDTO
{
    public function __construct(
        public readonly int $activityId,
        public readonly int $verifiedByUserId,
        public readonly bool $isValid,
        public readonly ?string $rejectionReason,
    ) {
    }
}
