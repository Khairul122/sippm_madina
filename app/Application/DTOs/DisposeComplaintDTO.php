<?php

declare(strict_types=1);

namespace App\Application\DTOs;

/**
 * Input for App\Application\UseCases\Complaint\DisposeComplaintUseCase.
 * Kominfo routes a DIVERIFIKASI complaint to one or more OPD/Camat targets
 * (BR-01/BR-02 — never Bupati/Wakil Bupati/Sekda).
 */
final class DisposeComplaintDTO
{
    /**
     * @param array<int, array{type: string, id: int}> $targets
     */
    public function __construct(
        public readonly int $complaintId,
        public readonly int $disposedByUserId,
        public readonly array $targets,
        public readonly ?string $note,
    ) {
    }
}
