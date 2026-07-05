<?php

declare(strict_types=1);

namespace App\Application\DTOs;

/**
 * Input for App\Application\UseCases\Complaint\SubmitComplaintUseCase.
 * Built by App\Http\Controllers\Api\ComplaintController from a validated
 * App\Http\Requests\Complaint\SubmitComplaintRequest.
 */
final class SubmitComplaintDTO
{
    /**
     * @param string[] $attachmentPaths storage-relative paths, already uploaded
     */
    public function __construct(
        public readonly int $userId,
        public readonly string $title,
        public readonly string $description,
        public readonly string $category,
        public readonly string $targetType,
        public readonly ?int $targetId,
        public readonly ?float $latitude,
        public readonly ?float $longitude,
        public readonly array $attachmentPaths = [],
    ) {
    }
}
