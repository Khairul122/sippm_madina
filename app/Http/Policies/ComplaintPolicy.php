<?php

namespace App\Http\Policies;

use App\Infrastructure\Persistence\Eloquent\Models\Complaint;
use App\Infrastructure\Persistence\Eloquent\Models\User;

/**
 * Object-level authorization for Complaint actions, layered on top of the
 * role-level `role:`/`permission:` middleware (see routes/api.php,
 * routes/web.php, database/seeders/RolePermissionSeeder.php).
 */
class ComplaintPolicy
{
    public function __construct()
    {
        //
    }

    public function viewAny(User $user): bool
    {
        // Every authenticated role may list complaints — masyarakat's
        // listing is scoped to their own complaints by the controller
        // query filter (see ComplaintController@index), not here.
        return true;
    }

    public function view(User $user, Complaint $complaint): bool
    {
        if ($user->hasRole('masyarakat')) {
            return $complaint->user_id === $user->id;
        }

        if ($user->hasRole('opd')) {
            return $complaint->dispositions()->where('disposed_to_type', 'opd')->where('disposed_to_id', $user->opd_id)->exists();
        }

        if ($user->hasRole('camat')) {
            return $complaint->dispositions()->where('disposed_to_type', 'camat')->where('disposed_to_id', $user->kecamatan_id)->exists();
        }

        // kominfo, bupati, wakil_bupati, sekda: full visibility (monitoring roles).
        return $user->hasAnyRole(['kominfo', 'bupati', 'wakil_bupati', 'sekda']);
    }

    public function create(User $user): bool
    {
        return $user->hasRole('masyarakat');
    }

    public function verify(User $user, Complaint $complaint): bool
    {
        return $user->hasRole('kominfo');
    }

    public function dispose(User $user, Complaint $complaint): bool
    {
        return $user->hasRole('kominfo');
    }

    public function handle(User $user, Complaint $complaint): bool
    {
        if ($user->hasRole('opd')) {
            return $complaint->dispositions()->where('disposed_to_type', 'opd')->where('disposed_to_id', $user->opd_id)->exists();
        }

        if ($user->hasRole('camat')) {
            return $complaint->dispositions()->where('disposed_to_type', 'camat')->where('disposed_to_id', $user->kecamatan_id)->exists();
        }

        return false;
    }

    public function respond(User $user, Complaint $complaint): bool
    {
        return $user->hasRole('kominfo');
    }

    public function update(User $user, Complaint $complaint): bool
    {
        return false;
    }

    public function delete(User $user, Complaint $complaint): bool
    {
        return $user->hasRole('kominfo');
    }
}
