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
 * Dispatched when Kominfo sends the final official answer to the citizen
 * (-> SELESAI). Broadcast privately to the submitting citizen only.
 */
class ComplaintResolved implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly Complaint $complaint,
        public readonly string $responseText,
    ) {
    }

    /**
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('App.Models.User.'.$this->complaint->userId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'complaint.resolved';
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
