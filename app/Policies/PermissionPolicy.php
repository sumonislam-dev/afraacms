<?php

namespace App\Policies;

use App\Models\User;

class PermissionPolicy
{
    /**
     * Determine whether the user can view the permission list.
     *
     * Permissions are read-only in the admin panel, so this is the
     * only ability this policy needs to define.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('permissions.view');
    }
}
