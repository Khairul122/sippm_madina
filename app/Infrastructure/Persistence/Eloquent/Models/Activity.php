<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;

use App\Domain\Activity\ValueObjects\ActivityStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['title', 'description', 'actor_type', 'actor_id', 'date', 'location', 'status', 'rejection_reason'])]
class Activity extends Model
{
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'status' => ActivityStatus::class,
            'date' => 'date',
        ];
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function actor(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return HasMany<ActivityDocumentation, $this>
     */
    public function documentations(): HasMany
    {
        return $this->hasMany(ActivityDocumentation::class);
    }
}
