<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Custom notification model backed by the `notifications` table (see
 * migration 2026_07_05_074602). Distinct from Laravel's built-in
 * Illuminate\Notifications\DatabaseNotification — SIPPM Madina does not
 * use notify()/Notifiable's database channel.
 */
#[Fillable(['user_id', 'title', 'message', 'type', 'is_read', 'data'])]
class Notification extends Model
{
    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
            'data' => 'array',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
