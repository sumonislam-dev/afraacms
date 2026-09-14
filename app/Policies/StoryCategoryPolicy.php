<?php

namespace App\Policies;

use App\Models\StoryCategory;
use App\Models\User;

class StoryCategoryPolicy
{
    /**
     * Determine whether the user can view the list of categories.
     *
     * Categories are managed as part of the Success Stories module, so
     * they reuse the "stories.*" permission set rather than a module of
     * their own.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('stories.view');
    }

    /**
     * Determine whether the user can view the given category.
     */
    public function view(User $user, StoryCategory $category): bool
    {
        return $user->can('stories.view');
    }

    /**
     * Determine whether the user can create categories.
     */
    public function create(User $user): bool
    {
        return $user->can('stories.create');
    }

    /**
     * Determine whether the user can update the given category.
     */
    public function update(User $user, StoryCategory $category): bool
    {
        return $user->can('stories.edit');
    }

    /**
     * Determine whether the user can delete the given category.
     */
    public function delete(User $user, StoryCategory $category): bool
    {
        return $user->can('stories.delete');
    }
}
