<?php

namespace App\Http\Policies;

use App\Infrastructure\Persistence\Eloquent\Models\User;

/**
 * User management (kelola_pengguna, Kominfo-only per PRD 4.2).
 */
class UserPolicy
{
    public function __construct()
    {
        //
    }

    public function viewAny(User $user): bool
    {
        return $user->hasRole('kominfo');
    }

    public function view(User $user, User $target): bool
    {
        return $user->hasRole('kominfo') || $user->id === $target->id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('kominfo');
    }

    public function update(User $user, User $target): bool
    {
        return $user->hasRole('kominfo');
    }

    public function deactivate(User $user, User $target): bool
    {
        return $user->hasRole('kominfo') && $user->id !== $target->id;
    }

    public function delete(User $user, User $target): bool
    {
        return $user->hasRole('kominfo') && $user->id !== $target->id;
    }
}
