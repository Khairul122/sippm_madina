<?php

declare(strict_types=1);

namespace App\Application\UseCases\Complaint;

use App\Application\DTOs\VerifyComplaintDTO;
use App\Domain\Complaint\Entities\Complaint;
use App\Domain\Complaint\Repositories\ComplaintRepositoryInterface;
use App\Domain\Complaint\Repositories\ComplaintStatusHistoryRepositoryInterface;
use App\Domain\Complaint\Rules\StatusTransitionGuard;
use App\Domain\Complaint\ValueObjects\ComplaintStatus;
use App\Infrastructure\Broadcasting\Events\ComplaintVerified;
use DomainException;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * Kominfo verifies a DIAJUKAN complaint: valid -> DIVERIFIKASI, invalid
 * -> DITOLAK (rejection reason mandatory). BR-04 transition whitelist is
 * enforced via StatusTransitionGuard.
 */
final class VerifyComplaintUseCase
{
    private const ACTING_ROLE = 'kominfo';

    public function __construct(
        private readonly ComplaintRepositoryInterface $complaints,
        private readonly ComplaintStatusHistoryRepositoryInterface $statusHistories,
    ) {
    }

    public function execute(VerifyComplaintDTO $dto): Complaint
    {
        $complaint = $this->complaints->findById($dto->complaintId);

        if ($complaint === null) {
            throw new InvalidArgumentException("Pengaduan #{$dto->complaintId} tidak ditemukan.");
        }

        $targetStatus = $dto->isValid ? ComplaintStatus::DIVERIFIKASI : ComplaintStatus::DITOLAK;

        // Defense in depth: Form Request already requires rejection_reason
        // when is_valid = false, but the UseCase never trusts the
        // presentation layer alone.
        if (! $dto->isValid && empty($dto->rejectionReason)) {
            throw new DomainException('Alasan penolakan wajib diisi ketika pengaduan dinyatakan tidak valid.');
        }

        StatusTransitionGuard::assertCanTransition($complaint->status, $targetStatus, self::ACTING_ROLE);

        return DB::transaction(function () use ($complaint, $dto, $targetStatus): Complaint {
            $this->complaints->updateStatus($complaint->id, $targetStatus, $dto->rejectionReason);

            $this->statusHistories->recordChange(
                complaintId: $complaint->id,
                status: $targetStatus,
                note: $dto->isValid ? $dto->note : $dto->rejectionReason,
                changedByUserId: $dto->verifiedByUserId,
            );

            $updated = $complaint->withStatus($targetStatus, $dto->rejectionReason);

            event(new ComplaintVerified($updated, $dto->isValid, $dto->rejectionReason, $complaint->status));

            return $updated;
        });
    }
}
