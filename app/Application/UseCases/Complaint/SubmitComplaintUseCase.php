<?php

declare(strict_types=1);

namespace App\Application\UseCases\Complaint;

use App\Application\DTOs\SubmitComplaintDTO;
use App\Domain\Complaint\Entities\Complaint;
use App\Domain\Complaint\Repositories\ComplaintRepositoryInterface;
use App\Domain\Complaint\Repositories\ComplaintStatusHistoryRepositoryInterface;
use App\Domain\Complaint\Rules\TicketNumberGeneratorRule;
use App\Domain\Complaint\ValueObjects\ComplaintStatus;
use App\Domain\Complaint\ValueObjects\TargetType;
use App\Infrastructure\Broadcasting\Events\ComplaintSubmitted;
use App\Infrastructure\Persistence\Eloquent\Models\ComplaintAttachment;
use Illuminate\Support\Facades\DB;

/**
 * A citizen (masyarakat) submits a new complaint. Generates the ticket
 * number (BR-03), persists the complaint with status DIAJUKAN, stores any
 * attachments, records the initial status history row, and broadcasts
 * ComplaintSubmitted to Kominfo.
 */
final class SubmitComplaintUseCase
{
    public function __construct(
        private readonly ComplaintRepositoryInterface $complaints,
        private readonly ComplaintStatusHistoryRepositoryInterface $statusHistories,
    ) {
    }

    public function execute(SubmitComplaintDTO $dto): Complaint
    {
        return DB::transaction(function () use ($dto): Complaint {
            $year = (int) date('Y');
            $sequenceSoFar = $this->complaints->countForYear($year);
            $ticketNumber = TicketNumberGeneratorRule::generate($year, $sequenceSoFar);

            $complaint = new Complaint(
                id: null,
                ticketNumber: $ticketNumber,
                userId: $dto->userId,
                title: $dto->title,
                description: $dto->description,
                category: $dto->category,
                targetType: TargetType::from($dto->targetType),
                targetId: $dto->targetId,
                status: ComplaintStatus::DIAJUKAN,
                latitude: $dto->latitude,
                longitude: $dto->longitude,
            );

            $saved = $this->complaints->save($complaint);

            foreach ($dto->attachmentPaths as $path) {
                ComplaintAttachment::query()->create([
                    'complaint_id' => $saved->id,
                    'file_path' => $path,
                    'file_type' => pathinfo($path, PATHINFO_EXTENSION),
                ]);
            }

            $this->statusHistories->recordChange(
                complaintId: $saved->id,
                status: ComplaintStatus::DIAJUKAN,
                note: 'Pengaduan diajukan oleh masyarakat.',
                changedByUserId: $dto->userId,
            );

            event(new ComplaintSubmitted($saved));

            return $saved;
        });
    }
}
