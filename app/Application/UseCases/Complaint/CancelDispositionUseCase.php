<?php

declare(strict_types=1);

namespace App\Application\UseCases\Complaint;

use App\Application\DTOs\CancelDispositionDTO;
use App\Domain\Complaint\Entities\Complaint;
use App\Domain\Complaint\Repositories\ComplaintRepositoryInterface;
use App\Domain\Complaint\Repositories\ComplaintStatusHistoryRepositoryInterface;
use App\Domain\Complaint\Repositories\DispositionRepositoryInterface;
use App\Domain\Complaint\Rules\StatusTransitionGuard;
use App\Domain\Complaint\ValueObjects\ComplaintStatus;
use App\Domain\Complaint\ValueObjects\TargetType;
use App\Infrastructure\Broadcasting\Events\ComplaintDispositionCancelled;
use InvalidArgumentException;
use Illuminate\Support\Facades\DB;

/**
 * Kominfo membatalkan disposisi yang salah target (-> kembali ke
 * DIVERIFIKASI, supaya bisa didisposisikan ulang lewat
 * DisposeComplaintUseCase). Hanya bisa dijalankan selama status masih
 * DIPROSES — StatusTransitionGuard menolak begitu status sudah
 * DITINDAKLANJUTI (OPD/Camat sudah mengirim laporan penanganan), jadi
 * tidak ada risiko "menghapus" pekerjaan yang sudah dilakukan unit terkait.
 */
final class CancelDispositionUseCase
{
    private const ACTING_ROLE = 'kominfo';

    public function __construct(
        private readonly ComplaintRepositoryInterface $complaints,
        private readonly ComplaintStatusHistoryRepositoryInterface $statusHistories,
        private readonly DispositionRepositoryInterface $dispositions,
    ) {
    }

    public function execute(CancelDispositionDTO $dto): Complaint
    {
        $complaint = $this->complaints->findById($dto->complaintId);

        if ($complaint === null) {
            throw new InvalidArgumentException("Pengaduan #{$dto->complaintId} tidak ditemukan.");
        }

        $activeDispositions = array_filter(
            $this->dispositions->listForComplaint($dto->complaintId),
            fn (array $d) => ($d['cancelled_at'] ?? null) === null,
        );

        if (empty($activeDispositions)) {
            throw new InvalidArgumentException('Tidak ada disposisi aktif untuk dibatalkan pada pengaduan ini.');
        }

        StatusTransitionGuard::assertCanTransition($complaint->status, ComplaintStatus::DIVERIFIKASI, self::ACTING_ROLE);

        return DB::transaction(function () use ($complaint, $dto, $activeDispositions): Complaint {
            $this->dispositions->cancelActiveForComplaint($complaint->id, $dto->cancelledByUserId, $dto->note);

            $this->complaints->updateStatus($complaint->id, ComplaintStatus::DIVERIFIKASI);

            $this->statusHistories->recordChange(
                complaintId: $complaint->id,
                status: ComplaintStatus::DIVERIFIKASI,
                note: $dto->note ? "Disposisi dibatalkan: {$dto->note}" : 'Disposisi dibatalkan.',
                changedByUserId: $dto->cancelledByUserId,
            );

            $updated = $complaint->withStatus(ComplaintStatus::DIVERIFIKASI);

            foreach ($activeDispositions as $d) {
                event(new ComplaintDispositionCancelled(
                    $updated,
                    $d['disposed_to_type'] instanceof TargetType ? $d['disposed_to_type'] : TargetType::from($d['disposed_to_type']),
                    (int) $d['disposed_to_id'],
                    $complaint->status,
                ));
            }

            return $updated;
        });
    }
}
