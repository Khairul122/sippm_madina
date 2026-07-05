<?php

declare(strict_types=1);

namespace App\Application\UseCases\Complaint;

use App\Application\DTOs\HandleComplaintDTO;
use App\Domain\Complaint\Entities\Complaint;
use App\Domain\Complaint\Repositories\ComplaintRepositoryInterface;
use App\Domain\Complaint\Repositories\ComplaintStatusHistoryRepositoryInterface;
use App\Domain\Complaint\Rules\StatusTransitionGuard;
use App\Domain\Complaint\ValueObjects\ComplaintStatus;
use App\Infrastructure\Broadcasting\Events\ComplaintHandled;
use App\Infrastructure\Persistence\Eloquent\Models\ComplaintHandling;
use InvalidArgumentException;
use Illuminate\Support\Facades\DB;

/**
 * OPD/Camat reports back on a disposition assigned to them (-> DITINDAKLANJUTI).
 */
final class HandleComplaintUseCase
{
    public function __construct(
        private readonly ComplaintRepositoryInterface $complaints,
        private readonly ComplaintStatusHistoryRepositoryInterface $statusHistories,
    ) {
    }

    public function execute(HandleComplaintDTO $dto, string $actingRoleSlug): Complaint
    {
        $complaint = $this->complaints->findById($dto->complaintId);

        if ($complaint === null) {
            throw new InvalidArgumentException("Pengaduan #{$dto->complaintId} tidak ditemukan.");
        }

        StatusTransitionGuard::assertCanTransition($complaint->status, ComplaintStatus::DITINDAKLANJUTI, $actingRoleSlug);

        return DB::transaction(function () use ($complaint, $dto): Complaint {
            ComplaintHandling::query()->create([
                'complaint_id' => $complaint->id,
                'disposition_id' => $dto->dispositionId,
                'handled_by' => $dto->handledByUserId,
                'description' => $dto->description,
                'attachment_path' => $dto->attachmentPath,
            ]);

            $this->complaints->updateStatus($complaint->id, ComplaintStatus::DITINDAKLANJUTI);

            $this->statusHistories->recordChange(
                complaintId: $complaint->id,
                status: ComplaintStatus::DITINDAKLANJUTI,
                note: $dto->description,
                changedByUserId: $dto->handledByUserId,
            );

            $updated = $complaint->withStatus(ComplaintStatus::DITINDAKLANJUTI);

            event(new ComplaintHandled($updated));

            return $updated;
        });
    }
}
