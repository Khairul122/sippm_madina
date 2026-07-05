<?php

declare(strict_types=1);

namespace App\Application\UseCases\Complaint;

use App\Application\DTOs\DisposeComplaintDTO;
use App\Domain\Complaint\Entities\Complaint;
use App\Domain\Complaint\Repositories\ComplaintRepositoryInterface;
use App\Domain\Complaint\Repositories\ComplaintStatusHistoryRepositoryInterface;
use App\Domain\Complaint\Repositories\DispositionRepositoryInterface;
use App\Domain\Complaint\Rules\DispositionMustTargetOpdOrCamatRule;
use App\Domain\Complaint\Rules\StatusTransitionGuard;
use App\Domain\Complaint\ValueObjects\ComplaintStatus;
use App\Domain\Complaint\ValueObjects\TargetType;
use App\Infrastructure\Broadcasting\Events\ComplaintDisposed;
use InvalidArgumentException;
use Illuminate\Support\Facades\DB;

/**
 * Kominfo routes a DIVERIFIKASI complaint to one or more OPD/Camat targets
 * (-> DIPROSES). BR-01/BR-02 (never Bupati/Wakil Bupati/Sekda) is enforced
 * via DispositionMustTargetOpdOrCamatRule for EVERY target BEFORE any
 * disposition row is created — this is the single most critical guard in
 * the whole module, see AGENTS.md Don'ts.
 */
final class DisposeComplaintUseCase
{
    private const ACTING_ROLE = 'kominfo';

    public function __construct(
        private readonly ComplaintRepositoryInterface $complaints,
        private readonly ComplaintStatusHistoryRepositoryInterface $statusHistories,
        private readonly DispositionRepositoryInterface $dispositions,
    ) {
    }

    public function execute(DisposeComplaintDTO $dto): Complaint
    {
        $complaint = $this->complaints->findById($dto->complaintId);

        if ($complaint === null) {
            throw new InvalidArgumentException("Pengaduan #{$dto->complaintId} tidak ditemukan.");
        }

        if (empty($dto->targets)) {
            throw new InvalidArgumentException('Minimal satu target disposisi wajib diisi.');
        }

        // Validate every target BEFORE creating any disposition row —
        // fail fast and atomically, never partially disposition. Keyed by
        // the SAME index as $dto->targets (not re-indexed from 0) since
        // that array can arrive sparse/non-sequential — e.g. a Blade
        // checkbox list where only some boxes are checked still submits
        // its original loop index (targets[3], targets[7], ...).
        $targetTypes = [];
        foreach ($dto->targets as $index => $target) {
            $targetType = TargetType::from($target['type']);
            DispositionMustTargetOpdOrCamatRule::assert($targetType);
            $targetTypes[$index] = $targetType;
        }

        StatusTransitionGuard::assertCanTransition($complaint->status, ComplaintStatus::DIPROSES, self::ACTING_ROLE);

        return DB::transaction(function () use ($complaint, $dto, $targetTypes): Complaint {
            foreach ($dto->targets as $index => $target) {
                $this->dispositions->create(
                    complaintId: $complaint->id,
                    disposedToType: $targetTypes[$index],
                    disposedToId: (int) $target['id'],
                    disposedByUserId: $dto->disposedByUserId,
                    note: $dto->note,
                );
            }

            $this->complaints->updateStatus($complaint->id, ComplaintStatus::DIPROSES);

            $this->statusHistories->recordChange(
                complaintId: $complaint->id,
                status: ComplaintStatus::DIPROSES,
                note: $dto->note,
                changedByUserId: $dto->disposedByUserId,
            );

            $updated = $complaint->withStatus(ComplaintStatus::DIPROSES);

            foreach ($dto->targets as $index => $target) {
                event(new ComplaintDisposed($updated, $targetTypes[$index], (int) $target['id']));
            }

            return $updated;
        });
    }
}
