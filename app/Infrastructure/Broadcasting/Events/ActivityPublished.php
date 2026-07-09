<?php

declare(strict_types=1);

namespace App\Infrastructure\Broadcasting\Events;

use App\Domain\Activity\Entities\Activity;
use App\Domain\Activity\ValueObjects\ActivityStatus;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Dispatched when Kominfo publishes a verified activity (kegiatan) report
 * (-> DIPUBLIKASIKAN). This is the ONLY event in the system broadcast on a
 * public (non-private) channel — everything else is a PrivateChannel.
 */
class ActivityPublished implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly Activity $activity,
        public readonly ?ActivityStatus $previousStatus = null,
    ) {
    }

    /**
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('public-activities'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'activity.published';
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'title' => $this->activity->title,
            'date' => $this->activity->date->format('Y-m-d'),
            'location' => $this->activity->location,
        ];
    }
}
