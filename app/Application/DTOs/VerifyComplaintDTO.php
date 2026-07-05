<?php

declare(strict_types=1);

namespace App\Application\DTOs;

/**
 * Input for App\Application\UseCases\Complaint\VerifyComplaintUseCase.
 * Kominfo verifies a DIAJUKAN complaint as valid (-> DIVERIFIKASI) or
 * invalid (-> DITOLAK, rejectionReason required).
 */
final class VerifyComplaintDTO
{
    public function __construct(
        public readonly int $complaintId,
        public readonly int $verifiedByUserId,
        public readonly bool $isValid,
        public readonly ?string $note,
        public readonly ?string $rejectionReason,
    ) {
    }
}
