<?php

namespace App\Policies;

use App\Models\TeamCategory;
use App\Models\User;

class TeamCategoryPolicy
{
    /**
     * Determine whether the user can view the list of categories.
     *
     * Categories are managed as part of the Team module, so they reuse
     * the "team.*" permission set rather than a module of their own.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('team.view');
    }

    /**
     * Determine whether the user can view the given category.
     */
    public function view(User $user, TeamCategory $category): bool
    {
        return $user->can('team.view');
    }

    /**
     * Determine whether the user can create categories.
     */
    public function create(User $user): bool
    {
        return $user->can('team.create');
    }

    /**
     * Determine whether the user can update the given category.
     */
    public function update(User $user, TeamCategory $category): bool
    {
        return $user->can('team.edit');
    }

    /**
     * Determine whether the user can delete the given category.
     */
    public function delete(User $user, TeamCategory $category): bool
    {
        return $user->can('team.delete');
    }
}
