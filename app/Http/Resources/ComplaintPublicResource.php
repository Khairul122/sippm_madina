<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Public tracking payload (no authentication). MUST NEVER expose internal
 * ids (complaint id, user_id, disposition ids, handler ids) — only the
 * ticket number and citizen-facing fields (AGENTS.md Don'ts).
 */
class ComplaintPublicResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'ticket_number' => $this->ticket_number,
            'title' => $this->title,
            'category' => $this->category,
            'status' => $this->status instanceof \BackedEnum ? $this->status->value : $this->status,
            'status_label' => $this->status?->label(),
            'rejection_reason' => $this->rejection_reason,
            'created_at' => (string) $this->created_at,
            'status_histories' => $this->whenLoaded('statusHistories', fn () => $this->statusHistories->map(fn ($h) => [
                'status' => $h->status instanceof \BackedEnum ? $h->status->value : $h->status,
                'status_label' => $h->status?->label(),
                'note' => $h->note,
                'created_at' => (string) $h->created_at,
            ])),
        ];
    }
}
