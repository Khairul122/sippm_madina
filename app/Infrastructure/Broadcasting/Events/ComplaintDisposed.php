<?php

declare(strict_types=1);

namespace App\Infrastructure\Broadcasting\Events;

use App\Domain\Complaint\Entities\Complaint;
use App\Domain\Complaint\ValueObjects\TargetType;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Dispatched once per disposition target after Kominfo routes a complaint
 * to an OPD/Camat (BR-01/BR-02 already enforced by
 * DispositionMustTargetOpdOrCamatRule before this event exists). Broadcast
 * privately to that specific OPD/Camat channel.
 */
class ComplaintDisposed implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly Complaint $complaint,
        public readonly TargetType $disposedToType,
        public readonly int $disposedToId,
    ) {
    }

    /**
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        $prefix = $this->disposedToType === TargetType::OPD ? 'channel-opd.' : 'channel-camat.';

        return [
            new PrivateChannel($prefix.$this->disposedToId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'complaint.disposed';
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
