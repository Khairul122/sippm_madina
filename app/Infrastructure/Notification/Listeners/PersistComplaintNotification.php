<?php

declare(strict_types=1);

namespace App\Infrastructure\Notification\Listeners;

use App\Domain\Notification\Entities\NotificationMessage;
use App\Domain\Notification\Repositories\NotificationRepositoryInterface;
use App\Infrastructure\Broadcasting\Events\ComplaintDisposed;
use App\Infrastructure\Broadcasting\Events\ComplaintHandled;
use App\Infrastructure\Broadcasting\Events\ComplaintResolved;
use App\Infrastructure\Broadcasting\Events\ComplaintSubmitted;
use App\Infrastructure\Broadcasting\Events\ComplaintVerified;
use App\Infrastructure\Persistence\Eloquent\Models\User;

/**
 * Persists a human-readable in-app notification row for every Complaint
 * event, independent of the WebSocket broadcast — so a user who was
 * offline when the event fired still sees it next time they log in.
 *
 * Deliberately NOT queued (no ShouldQueue): per explicit user decision,
 * notifications must land immediately, not wait for a queue worker that
 * may not be running. Audit logging and WebSocket broadcasting remain
 * queued (see RecordAuditLog, and ShouldBroadcast on the events
 * themselves) since those are non-blocking side effects — only the
 * user-facing notification row needs to exist synchronously.
 */
class PersistComplaintNotification
{
    public function __construct(
        private readonly NotificationRepositoryInterface $notifications,
    ) {
    }

    public function handle(object $event): void
    {
        if ($event instanceof ComplaintDisposed) {
            $unitColumn = $event->disposedToType->value === 'opd' ? 'opd_id' : 'kecamatan_id';
            $userIds = User::query()->where($unitColumn, $event->disposedToId)->pluck('id');

            foreach ($userIds as $userId) {
                $this->store(
                    $userId,
                    'Pengaduan baru didisposisikan',
                    "Pengaduan {$event->complaint->ticketNumber} didisposisikan ke unit Anda.",
                    $event::class,
                );
            }

            return;
        }

        [$userIds, $title, $message] = match (true) {
            $event instanceof ComplaintSubmitted => [
                $this->kominfoUserIds(),
                'Pengaduan baru masuk',
                "Pengaduan {$event->complaint->ticketNumber} telah diajukan dan menunggu verifikasi.",
            ],
            $event instanceof ComplaintVerified => [
                [$event->complaint->userId],
                $event->isValid ? 'Pengaduan Anda telah diverifikasi' : 'Pengaduan Anda ditolak',
                $event->isValid
                    ? "Pengaduan {$event->complaint->ticketNumber} telah diverifikasi dan akan didisposisikan."
                    : "Pengaduan {$event->complaint->ticketNumber} ditolak. Alasan: {$event->rejectionReason}",
            ],
            $event instanceof ComplaintHandled => [
                $this->kominfoUserIds(),
                'Pengaduan telah ditindaklanjuti',
                "Pengaduan {$event->complaint->ticketNumber} telah ditindaklanjuti oleh unit terkait.",
            ],
            $event instanceof ComplaintResolved => [
                [$event->complaint->userId],
                'Pengaduan Anda telah selesai',
                "Pengaduan {$event->complaint->ticketNumber} telah dijawab resmi oleh Kominfo.",
            ],
            default => [[], null, null],
        };

        if ($title === null) {
            return;
        }

        foreach ($userIds as $userId) {
            $this->store($userId, $title, $message, $event::class);
        }
    }

    private function store(int $userId, string $title, string $message, string $type): void
    {
        $this->notifications->create(new NotificationMessage(
            id: null,
            userId: $userId,
            title: $title,
            message: $message,
            type: $type,
        ));
    }

    /**
     * @return array<int, int>
     */
    private function kominfoUserIds(): array
    {
        return User::role('kominfo')->pluck('id')->all();
    }
}
