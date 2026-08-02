<?php

namespace App\Policies;

use App\Models\User;

class SettingPolicy
{
    /**
     * Determine whether the user can view the settings screen.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('settings.view');
    }

    /**
     * Determine whether the user can modify settings.
     */
    public function update(User $user): bool
    {
        return $user->can('settings.edit');
    }
}
