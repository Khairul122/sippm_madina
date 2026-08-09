<?php

declare(strict_types=1);

namespace App\Infrastructure\Notification\Listeners;

use App\Infrastructure\Broadcasting\Events\ActivityPublished;
use App\Infrastructure\Broadcasting\Events\ComplaintDisposed;
use App\Infrastructure\Broadcasting\Events\ComplaintDispositionCancelled;
use App\Infrastructure\Broadcasting\Events\ComplaintHandled;
use App\Infrastructure\Broadcasting\Events\ComplaintResolved;
use App\Infrastructure\Broadcasting\Events\ComplaintSubmitted;
use App\Infrastructure\Broadcasting\Events\ComplaintVerified;
use App\Infrastructure\Persistence\Eloquent\Models\AuditLog;

/**
 * Single centralized audit trail listener for every Complaint/Activity domain event.
 */
class RecordAuditLog
{
    public function handle(object $event): void
    {
        [$action, $modelType, $modelId, $oldData, $newData] = match (true) {
            $event instanceof ComplaintSubmitted => [
                'Pengaduan Diajukan',
                'complaint',
                $event->complaint->id,
                null,
                [
                    'ticket_number' => (string) $event->complaint->ticketNumber,
                    'title' => $event->complaint->title,
                    'status' => $event->complaint->status->value,
                ],
            ],
            $event instanceof ComplaintVerified => [
                $event->isValid ? 'Pengaduan Diverifikasi' : 'Pengaduan Ditolak',
                'complaint',
                $event->complaint->id,
                $event->previousStatus ? ['status' => $event->previousStatus->value] : null,
                [
                    'status' => $event->complaint->status->value,
                    'is_valid' => $event->isValid,
                    'rejection_reason' => $event->rejectionReason,
                ],
            ],
            $event instanceof ComplaintDisposed => [
                'Pengaduan Didisposisikan',
                'complaint',
                $event->complaint->id,
                $event->previousStatus ? ['status' => $event->previousStatus->value] : null,
                [
                    'status' => $event->complaint->status->value,
                    'disposed_to_type' => $event->disposedToType->value,
                    'disposed_to_id' => $event->disposedToId,
                ],
            ],
            $event instanceof ComplaintDispositionCancelled => [
                'Disposisi Pengaduan Dibatalkan',
                'complaint',
                $event->complaint->id,
                $event->previousStatus ? ['status' => $event->previousStatus->value] : null,
                [
                    'status' => $event->complaint->status->value,
                    'cancelled_target_type' => $event->cancelledTargetType->value,
                    'cancelled_target_id' => $event->cancelledTargetId,
                ],
            ],
            $event instanceof ComplaintHandled => [
                'Pengaduan Ditindaklanjuti',
                'complaint',
                $event->complaint->id,
                $event->previousStatus ? ['status' => $event->previousStatus->value] : null,
                ['status' => $event->complaint->status->value],
            ],
            $event instanceof ComplaintResolved => [
                'Pengaduan Selesai & Ditanggap',
                'complaint',
                $event->complaint->id,
                $event->previousStatus ? ['status' => $event->previousStatus->value] : null,
                [
                    'status' => $event->complaint->status->value,
                    'response_text' => $event->responseText,
                ],
            ],
            $event instanceof ActivityPublished => [
                'Laporan Kegiatan Dipublikasikan',
                'activity',
                $event->activity->id,
                $event->previousStatus ? ['status' => $event->previousStatus->value] : null,
                ['status' => $event->activity->status->value],
            ],
            default => [null, null, null, null, null],
        };

        if ($action === null) {
            return;
        }

        AuditLog::record(
            action: $action,
            modelType: $modelType,
            modelId: $modelId,
            oldData: $oldData,
            newData: $newData
        );
    }
}
