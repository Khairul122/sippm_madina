<?php

declare(strict_types=1);

namespace App\Infrastructure\Broadcasting\Events;

use App\Domain\Complaint\Entities\Complaint;
use App\Domain\Complaint\ValueObjects\ComplaintStatus;
use App\Domain\Complaint\ValueObjects\TargetType;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Dispatched once per previously-active disposition target after Kominfo
 * membatalkan disposisi yang salah target (complaint kembali ke
 * DIVERIFIKASI). Broadcast privately to that specific OPD/Camat channel —
 * simetris dengan ComplaintDisposed, supaya unit yang sebelumnya menerima
 * disposisi tahu bahwa tugas itu ditarik kembali.
 */
class ComplaintDispositionCancelled implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly Complaint $complaint,
        public readonly TargetType $cancelledTargetType,
        public readonly int $cancelledTargetId,
        public readonly ?ComplaintStatus $previousStatus = null,
    ) {
    }

    /**
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        $prefix = $this->cancelledTargetType === TargetType::OPD ? 'channel-opd.' : 'channel-camat.';

        return [
            new PrivateChannel($prefix.$this->cancelledTargetId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'complaint.disposition-cancelled';
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
