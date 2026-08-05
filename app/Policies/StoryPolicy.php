<?php

namespace App\Policies;

use App\Models\Story;
use App\Models\User;

class StoryPolicy
{
    /**
     * Determine whether the user can view the list of stories.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('stories.view');
    }

    /**
     * Determine whether the user can view the given story.
     */
    public function view(User $user, Story $story): bool
    {
        return $user->can('stories.view');
    }

    /**
     * Determine whether the user can create stories.
     */
    public function create(User $user): bool
    {
        return $user->can('stories.create');
    }

    /**
     * Determine whether the user can update the given story.
     */
    public function update(User $user, Story $story): bool
    {
        return $user->can('stories.edit');
    }

    /**
     * Determine whether the user can delete the given story.
     */
    public function delete(User $user, Story $story): bool
    {
        return $user->can('stories.delete');
    }

    /**
     * Determine whether the user can restore the given trashed story.
     */
    public function restore(User $user, Story $story): bool
    {
        return $user->can('stories.delete');
    }

    /**
     * Determine whether the user can permanently delete the given trashed story.
     */
    public function forceDelete(User $user, Story $story): bool
    {
        return $user->can('stories.delete');
    }
}
