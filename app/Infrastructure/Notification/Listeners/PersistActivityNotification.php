<?php

declare(strict_types=1);

namespace App\Infrastructure\Notification\Listeners;

use App\Domain\Notification\Entities\NotificationMessage;
use App\Domain\Notification\Repositories\NotificationRepositoryInterface;
use App\Infrastructure\Broadcasting\Events\ActivityPublished;
use App\Infrastructure\Persistence\Eloquent\Models\User;

/**
 * Persists an in-app notification when a kegiatan (activity) is published.
 * Publication itself is public (broadcast on a public channel with no
 * auth), but the persisted notification row requires a concrete user_id
 * (FK, not nullable) — so this records a confirmation for the Kominfo
 * users who manage the feed, rather than fanning out to every citizen.
 *
 * Deliberately NOT queued (no ShouldQueue) — see PersistComplaintNotification
 * for the rationale: notifications must land immediately regardless of
 * whether a queue worker is running.
 */
class PersistActivityNotification
{
    public function __construct(
        private readonly NotificationRepositoryInterface $notifications,
    ) {
    }

    public function handle(ActivityPublished $event): void
    {
        $kominfoUserIds = User::role('kominfo')->pluck('id');

        foreach ($kominfoUserIds as $userId) {
            $this->notifications->create(new NotificationMessage(
                id: null,
                userId: $userId,
                title: 'Kegiatan dipublikasikan',
                message: "Kegiatan \"{$event->activity->title}\" telah dipublikasikan ke feed publik.",
                type: ActivityPublished::class,
            ));
        }
    }
}
