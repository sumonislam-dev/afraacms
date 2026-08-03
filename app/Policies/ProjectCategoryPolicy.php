<?php

namespace App\Policies;

use App\Models\ProjectCategory;
use App\Models\User;

class ProjectCategoryPolicy
{
    /**
     * Determine whether the user can view the list of categories.
     *
     * Categories are managed as part of the Projects module, so they
     * reuse the "projects.*" permission set rather than a module of
     * their own.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('projects.view');
    }

    /**
     * Determine whether the user can view the given category.
     */
    public function view(User $user, ProjectCategory $category): bool
    {
        return $user->can('projects.view');
    }

    /**
     * Determine whether the user can create categories.
     */
    public function create(User $user): bool
    {
        return $user->can('projects.create');
    }

    /**
     * Determine whether the user can update the given category.
     */
    public function update(User $user, ProjectCategory $category): bool
    {
        return $user->can('projects.edit');
    }

    /**
     * Determine whether the user can delete the given category.
     */
    public function delete(User $user, ProjectCategory $category): bool
    {
        return $user->can('projects.delete');
    }
}
