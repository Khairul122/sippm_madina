<?php

declare(strict_types=1);

namespace App\Application\UseCases\Complaint;

use App\Domain\Complaint\Repositories\ComplaintRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Models\AuditLog;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

final class DeleteComplaintUseCase
{
    public function __construct(private readonly ComplaintRepositoryInterface $complaints)
    {
    }

    public function execute(int $complaintId, int $deletedByUserId): void
    {
        DB::transaction(function () use ($complaintId, $deletedByUserId): void {
            $complaint = $this->complaints->findById($complaintId);

            if ($complaint === null) {
                throw new InvalidArgumentException("Pengaduan #{$complaintId} tidak ditemukan.");
            }

            $this->complaints->delete($complaintId);

            AuditLog::record(
                action: 'Pengaduan Dihapus',
                modelType: 'complaint',
                modelId: $complaintId,
                oldData: ['ticket_number' => (string) $complaint->ticketNumber],
                newData: [],
                userId: $deletedByUserId,
            );
        });
    }
}
