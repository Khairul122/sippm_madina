<?php

declare(strict_types=1);

namespace App\Application\UseCases\Complaint;

use App\Application\DTOs\ResolveComplaintDTO;
use App\Domain\Complaint\Entities\Complaint;
use App\Domain\Complaint\Repositories\ComplaintRepositoryInterface;
use App\Domain\Complaint\Repositories\ComplaintStatusHistoryRepositoryInterface;
use App\Domain\Complaint\Rules\StatusTransitionGuard;
use App\Domain\Complaint\ValueObjects\ComplaintStatus;
use App\Infrastructure\Broadcasting\Events\ComplaintResolved;
use App\Infrastructure\Persistence\Eloquent\Models\ComplaintResponse;
use InvalidArgumentException;
use Illuminate\Support\Facades\DB;

/**
 * Kominfo sends the final official answer to the citizen (-> SELESAI).
 */
final class ResolveComplaintUseCase
{
    private const ACTING_ROLE = 'kominfo';

    public function __construct(
        private readonly ComplaintRepositoryInterface $complaints,
        private readonly ComplaintStatusHistoryRepositoryInterface $statusHistories,
    ) {
    }

    public function execute(ResolveComplaintDTO $dto): Complaint
    {
        $complaint = $this->complaints->findById($dto->complaintId);

        if ($complaint === null) {
            throw new InvalidArgumentException("Pengaduan #{$dto->complaintId} tidak ditemukan.");
        }

        StatusTransitionGuard::assertCanTransition($complaint->status, ComplaintStatus::SELESAI, self::ACTING_ROLE);

        return DB::transaction(function () use ($complaint, $dto): Complaint {
            ComplaintResponse::query()->create([
                'complaint_id' => $complaint->id,
                'response_text' => $dto->responseText,
                'responded_by' => $dto->respondedByUserId,
            ]);

            $this->complaints->updateStatus($complaint->id, ComplaintStatus::SELESAI);

            $this->statusHistories->recordChange(
                complaintId: $complaint->id,
                status: ComplaintStatus::SELESAI,
                note: $dto->responseText,
                changedByUserId: $dto->respondedByUserId,
            );

            $updated = $complaint->withStatus(ComplaintStatus::SELESAI);

            event(new ComplaintResolved($updated, $dto->responseText));

            return $updated;
        });
    }
}
