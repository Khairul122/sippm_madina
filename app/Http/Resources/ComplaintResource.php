<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Full complaint payload for internal roles (Kominfo/OPD/Camat/pimpinan).
 * Expects an App\Infrastructure\Persistence\Eloquent\Models\Complaint
 * (optionally eager-loaded with attachments/statusHistories/dispositions/
 * handlings/response/user) as the wrapped resource.
 */
class ComplaintResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ticket_number' => $this->ticket_number,
            'title' => $this->title,
            'description' => $this->description,
            'category' => $this->category,
            'target_type' => $this->target_type instanceof \BackedEnum ? $this->target_type->value : $this->target_type,
            'target_id' => $this->target_id,
            'status' => $this->status instanceof \BackedEnum ? $this->status->value : $this->status,
            'status_label' => $this->status?->label(),
            'rejection_reason' => $this->rejection_reason,
            'latitude' => $this->latitude !== null ? (float) $this->latitude : null,
            'longitude' => $this->longitude !== null ? (float) $this->longitude : null,
            'user_id' => $this->user_id,
            'user' => $this->whenLoaded('user', fn () => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ]),
            'attachments' => $this->whenLoaded('attachments', fn () => $this->attachments->map(fn ($a) => [
                'id' => $a->id,
                'file_path' => $a->file_path,
                'file_type' => $a->file_type,
            ])),
            'status_histories' => $this->whenLoaded('statusHistories', fn () => $this->statusHistories->map(fn ($h) => [
                'status' => $h->status instanceof \BackedEnum ? $h->status->value : $h->status,
                'note' => $h->note,
                'changed_by' => $h->changed_by,
                'created_at' => (string) $h->created_at,
            ])),
            'dispositions' => $this->whenLoaded('dispositions', fn () => $this->dispositions->map(fn ($d) => [
                'id' => $d->id,
                'disposed_to_type' => $d->disposed_to_type,
                'disposed_to_id' => $d->disposed_to_id,
                'note' => $d->note,
                'created_at' => (string) $d->created_at,
            ])),
            'handlings' => $this->whenLoaded('handlings', fn () => $this->handlings->map(fn ($h) => [
                'id' => $h->id,
                'description' => $h->description,
                'attachment_path' => $h->attachment_path,
                'created_at' => (string) $h->created_at,
            ])),
            'response' => $this->whenLoaded('response', fn () => $this->response ? [
                'response_text' => $this->response->response_text,
                'responded_by' => $this->response->responded_by,
                'created_at' => (string) $this->response->created_at,
            ] : null),
            'created_at' => (string) $this->created_at,
            'updated_at' => (string) $this->updated_at,
        ];
    }
}
