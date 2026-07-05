<?php

namespace App\Http\Policies;

use App\Infrastructure\Persistence\Eloquent\Models\Activity;
use App\Infrastructure\Persistence\Eloquent\Models\User;

/**
 * Object-level authorization for Activity (kegiatan) actions.
 */
class ActivityPolicy
{
    public function __construct()
    {
        //
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Activity $activity): bool
    {
        if ($user->hasRole('opd')) {
            return $activity->actor_type === 'opd' && $activity->actor_id === $user->opd_id;
        }

        if ($user->hasRole('camat')) {
            return $activity->actor_type === 'kecamatan' && $activity->actor_id === $user->kecamatan_id;
        }

        return $user->hasAnyRole(['kominfo', 'bupati', 'wakil_bupati', 'sekda']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['opd', 'camat']);
    }

    public function verify(User $user, Activity $activity): bool
    {
        return $user->hasRole('kominfo');
    }

    public function publish(User $user, Activity $activity): bool
    {
        return $user->hasRole('kominfo');
    }

    public function update(User $user, Activity $activity): bool
    {
        if ($user->hasRole('opd')) {
            return $activity->actor_type === 'opd' && $activity->actor_id === $user->opd_id;
        }

        if ($user->hasRole('camat')) {
            return $activity->actor_type === 'kecamatan' && $activity->actor_id === $user->kecamatan_id;
        }

        return false;
    }

    public function delete(User $user, Activity $activity): bool
    {
        return $user->hasRole('kominfo');
    }
}
