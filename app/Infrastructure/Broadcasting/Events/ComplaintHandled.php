<?php

declare(strict_types=1);

namespace App\Infrastructure\Broadcasting\Events;

use App\Domain\Complaint\Entities\Complaint;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Dispatched when an OPD/Camat submits its handling report for a
 * disposition (-> DITINDAKLANJUTI). Broadcast to Kominfo so they know a
 * final official response is now needed.
 */
class ComplaintHandled implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly Complaint $complaint,
    ) {
    }

    /**
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-kominfo'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'complaint.handled';
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'ticket_number' => (string) $this->complaint->ticketNumber,
            'title' => $this->complaint->title,
            'status' => $this->complaint->status->value,
        ];
    }
}
