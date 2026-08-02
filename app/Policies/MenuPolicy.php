<?php

namespace App\Policies;

use App\Models\Menu;
use App\Models\User;

class MenuPolicy
{
    /**
     * Determine whether the user can view the list of menus.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('menus.view');
    }

    /**
     * Determine whether the user can view the given menu.
     */
    public function view(User $user, Menu $menu): bool
    {
        return $user->can('menus.view');
    }

    /**
     * Determine whether the user can create menus.
     */
    public function create(User $user): bool
    {
        return $user->can('menus.create');
    }

    /**
     * Determine whether the user can update the given menu (including its items).
     */
    public function update(User $user, Menu $menu): bool
    {
        return $user->can('menus.edit');
    }

    /**
     * Determine whether the user can delete the given menu.
     */
    public function delete(User $user, Menu $menu): bool
    {
        return $user->can('menus.delete');
    }
}
