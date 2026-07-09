<?php

declare(strict_types=1);

namespace App\Infrastructure\Notification\Listeners;

use App\Infrastructure\Broadcasting\Events\ActivityPublished;
use App\Infrastructure\Broadcasting\Events\ComplaintDisposed;
use App\Infrastructure\Broadcasting\Events\ComplaintHandled;
use App\Infrastructure\Broadcasting\Events\ComplaintResolved;
use App\Infrastructure\Broadcasting\Events\ComplaintSubmitted;
use App\Infrastructure\Broadcasting\Events\ComplaintVerified;
use App\Infrastructure\Persistence\Eloquent\Models\AuditLog;

/**
 * Single centralized audit trail listener for every Complaint/Activity
 * domain event, instead of scattering `AuditLog::create()` calls across
 * every UseCase. Subscribed to each event class individually in
 * AppServiceProvider::boot() (all pointing at this one handle() method).
 */
class RecordAuditLog
{
    public function handle(object $event): void
    {
        [$modelType, $modelId, $oldData, $newData] = match (true) {
            $event instanceof ComplaintSubmitted => ['complaint', $event->complaint->id, null, [
                'ticket_number' => (string) $event->complaint->ticketNumber,
                'status' => $event->complaint->status->value,
            ]],
            $event instanceof ComplaintVerified => ['complaint', $event->complaint->id,
                $event->previousStatus ? ['status' => $event->previousStatus->value] : null,
                [
                    'status' => $event->complaint->status->value,
                    'is_valid' => $event->isValid,
                    'rejection_reason' => $event->rejectionReason,
                ],
            ],
            $event instanceof ComplaintDisposed => ['complaint', $event->complaint->id,
                $event->previousStatus ? ['status' => $event->previousStatus->value] : null,
                [
                    'status' => $event->complaint->status->value,
                    'disposed_to_type' => $event->disposedToType->value,
                    'disposed_to_id' => $event->disposedToId,
                ],
            ],
            $event instanceof ComplaintHandled => ['complaint', $event->complaint->id,
                $event->previousStatus ? ['status' => $event->previousStatus->value] : null,
                ['status' => $event->complaint->status->value],
            ],
            $event instanceof ComplaintResolved => ['complaint', $event->complaint->id,
                $event->previousStatus ? ['status' => $event->previousStatus->value] : null,
                [
                    'status' => $event->complaint->status->value,
                    'response_text' => $event->responseText,
                ],
            ],
            $event instanceof ActivityPublished => ['activity', $event->activity->id,
                $event->previousStatus ? ['status' => $event->previousStatus->value] : null,
                ['status' => $event->activity->status->value],
            ],
            default => [null, null, null, null],
        };

        if ($modelType === null) {
            return;
        }

        AuditLog::query()->create([
            'user_id' => auth()->id(),
            'action' => $event::class,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'old_data' => $oldData,
            'new_data' => $newData,
            'ip_address' => request()?->ip(),
        ]);
    }
}
