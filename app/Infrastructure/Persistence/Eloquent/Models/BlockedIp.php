<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['ip_address', 'blocked_by', 'audit_log_id'])]
class BlockedIp extends Model
{
    public const UPDATED_AT = null;
}
