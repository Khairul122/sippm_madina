<?php

declare(strict_types=1);

namespace App\Application\DTOs;

/**
 * Input for App\Application\UseCases\Complaint\ResolveComplaintUseCase.
 * Kominfo sends the final official answer to the citizen (-> SELESAI).
 */
final class ResolveComplaintDTO
{
    public function __construct(
        public readonly int $complaintId,
        public readonly int $respondedByUserId,
        public readonly string $responseText,
    ) {
    }
}
