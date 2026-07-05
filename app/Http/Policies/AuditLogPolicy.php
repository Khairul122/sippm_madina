<?php

namespace App\Http\Policies;

use App\Infrastructure\Persistence\Eloquent\Models\AuditLog;
use App\Infrastructure\Persistence\Eloquent\Models\User;

/**
 * Audit log access (lihat_audit_log, Kominfo-only per PRD 4.2). Audit logs
 * can never be updated or deleted (enforced at the model level in
 * AuditLog), so this policy only ever needs read-oriented methods.
 */
class AuditLogPolicy
{
    public function __construct()
    {
        //
    }

    public function viewAny(User $user): bool
    {
        return $user->hasRole('kominfo');
    }

    public function view(User $user, AuditLog $auditLog): bool
    {
        return $user->hasRole('kominfo');
    }
}
