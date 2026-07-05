<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;

use App\Domain\Complaint\ValueObjects\ComplaintStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'ticket_number', 'user_id', 'title', 'description', 'category',
    'target_type', 'target_id', 'status', 'latitude', 'longitude', 'rejection_reason',
])]
class Complaint extends Model
{
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'status' => ComplaintStatus::class,
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<ComplaintAttachment, $this>
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(ComplaintAttachment::class);
    }

    /**
     * @return HasMany<ComplaintStatusHistory, $this>
     */
    public function statusHistories(): HasMany
    {
        return $this->hasMany(ComplaintStatusHistory::class);
    }

    /**
     * @return HasMany<Disposition, $this>
     */
    public function dispositions(): HasMany
    {
        return $this->hasMany(Disposition::class);
    }

    /**
     * @return HasOne<ComplaintResponse, $this>
     */
    public function response(): HasOne
    {
        return $this->hasOne(ComplaintResponse::class);
    }

    /**
     * @return HasMany<ComplaintHandling, $this>
     */
    public function handlings(): HasMany
    {
        return $this->hasMany(ComplaintHandling::class);
    }
}
