<?php

declare(strict_types=1);

namespace App\Application\DTOs;

/**
 * Input for App\Application\UseCases\Complaint\CancelDispositionUseCase.
 * Kominfo membatalkan disposisi yang salah target (-> kembali ke
 * DIVERIFIKASI) supaya bisa didisposisikan ulang.
 */
final class CancelDispositionDTO
{
    public function __construct(
        public readonly int $complaintId,
        public readonly int $cancelledByUserId,
        public readonly ?string $note,
    ) {
    }
}
