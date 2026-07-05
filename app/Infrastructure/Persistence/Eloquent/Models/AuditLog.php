<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RuntimeException;

/**
 * Audit logs are immutable by design (PRD DON'T: "audit log tidak pernah
 * dihapus"). This model has no `updated_at` column and blocks both the
 * `updating` and `deleting` Eloquent events so that even a stray
 * ->update()/->delete() call anywhere in the codebase fails loudly instead
 * of silently corrupting the audit trail.
 */
#[Fillable(['user_id', 'action', 'model_type', 'model_id', 'old_data', 'new_data', 'ip_address'])]
class AuditLog extends Model
{
    public const UPDATED_AT = null;

    protected function casts(): array
    {
        return [
            'old_data' => 'array',
            'new_data' => 'array',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::updating(function () {
            throw new RuntimeException('Audit log tidak dapat diubah.');
        });

        static::deleting(function () {
            throw new RuntimeException('Audit log tidak dapat dihapus.');
        });
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
