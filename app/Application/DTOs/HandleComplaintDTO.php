<?php

declare(strict_types=1);

namespace App\Application\DTOs;

/**
 * Input for App\Application\UseCases\Complaint\HandleComplaintUseCase.
 * OPD/Camat reports back on a disposition assigned to them.
 */
final class HandleComplaintDTO
{
    public function __construct(
        public readonly int $complaintId,
        public readonly int $dispositionId,
        public readonly int $handledByUserId,
        public readonly string $description,
        public readonly ?string $attachmentPath,
    ) {
    }
}
