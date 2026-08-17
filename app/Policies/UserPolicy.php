<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('users.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        return $user->can('users.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('users.create');
    }

    /**
     * Determine whether the user can update the model.
     *
     * Only a Super Admin may edit another Super Admin's account, so a
     * lower-privileged user with users.edit can't demote, deactivate, or
     * otherwise tamper with a Super Admin's account.
     */
    public function update(User $user, User $model): bool
    {
        if ($model->hasRole('Super Admin') && ! $user->hasRole('Super Admin')) {
            return false;
        }

        return $user->can('users.edit');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * Same Super-Admin-account protection as update(), plus: nobody -
     * including another Super Admin - can delete the last currently-active
     * Super Admin, so the system can never end up with zero.
     */
    public function delete(User $user, User $model): bool
    {
        if ($user->id === $model->id) {
            return false;
        }

        if ($model->hasRole('Super Admin') && ! $user->hasRole('Super Admin')) {
            return false;
        }

        if ($model->isLastActiveSuperAdmin()) {
            return false;
        }

        return $user->can('users.delete');
    }
}
