<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;

class RolePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('roles.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Role $role): bool
    {
        return $user->can('roles.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('roles.create');
    }

    /**
     * Determine whether the user can update the model.
     *
     * Only a Super Admin may edit the Super Admin role, so a lower-privileged
     * user with roles.edit cannot strip or change the permissions that
     * protect the platform's top role.
     */
    public function update(User $user, Role $role): bool
    {
        if ($role->name === 'Super Admin' && ! $user->hasRole('Super Admin')) {
            return false;
        }

        return $user->can('roles.edit');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * The Super Admin role can never be deleted, and any other role can
     * only be deleted once no users are assigned to it.
     */
    public function delete(User $user, Role $role): bool
    {
        if ($role->name === 'Super Admin') {
            return false;
        }

        return $user->can('roles.delete') && $role->users()->count() === 0;
    }
}
