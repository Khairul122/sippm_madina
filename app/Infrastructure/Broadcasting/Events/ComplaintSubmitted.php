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
 * Dispatched right after a citizen submits a new complaint (status
 * DIAJUKAN). Broadcast to Kominfo's private channel so verification staff
 * see new tickets without polling. Also consumed by
 * Infrastructure\Notification\Listeners\{RecordAuditLog,PersistComplaintNotification}.
 */
class ComplaintSubmitted implements ShouldBroadcast
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
        return 'complaint.submitted';
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'ticket_number' => (string) $this->complaint->ticketNumber,
            'title' => $this->complaint->title,
            'category' => $this->complaint->category,
            'status' => $this->complaint->status->value,
        ];
    }
}
