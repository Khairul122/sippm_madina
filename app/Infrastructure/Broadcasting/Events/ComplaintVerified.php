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
 * Dispatched when Kominfo verifies a complaint, either as valid
 * (-> DIVERIFIKASI) or invalid (-> DITOLAK, $rejectionReason set).
 * Broadcast privately to the submitting citizen only.
 */
class ComplaintVerified implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly Complaint $complaint,
        public readonly bool $isValid,
        public readonly ?string $rejectionReason = null,
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
        return $this->isValid ? 'complaint.verified' : 'complaint.rejected';
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
            'rejection_reason' => $this->rejectionReason,
        ];
    }
}
