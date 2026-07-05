<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'actor_type' => $this->actor_type,
            'actor_id' => $this->actor_id,
            'date' => $this->date instanceof \DateTimeInterface ? $this->date->format('Y-m-d') : $this->date,
            'location' => $this->location,
            'status' => $this->status instanceof \BackedEnum ? $this->status->value : $this->status,
            'status_label' => $this->status?->label(),
            'rejection_reason' => $this->rejection_reason,
            'documentations' => $this->whenLoaded('documentations', fn () => $this->documentations->map(fn ($d) => [
                'id' => $d->id,
                'file_path' => $d->file_path,
                'caption' => $d->caption,
            ])),
            'created_at' => (string) $this->created_at,
        ];
    }
}
