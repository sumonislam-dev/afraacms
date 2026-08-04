<?php

namespace App\Policies;

use App\Models\NewsCategory;
use App\Models\User;

class NewsCategoryPolicy
{
    /**
     * Determine whether the user can view the list of categories.
     *
     * Categories are managed as part of the News module, so they reuse
     * the "news.*" permission set rather than a module of their own.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('news.view');
    }

    /**
     * Determine whether the user can view the given category.
     */
    public function view(User $user, NewsCategory $category): bool
    {
        return $user->can('news.view');
    }

    /**
     * Determine whether the user can create categories.
     */
    public function create(User $user): bool
    {
        return $user->can('news.create');
    }

    /**
     * Determine whether the user can update the given category.
     */
    public function update(User $user, NewsCategory $category): bool
    {
        return $user->can('news.edit');
    }

    /**
     * Determine whether the user can delete the given category.
     */
    public function delete(User $user, NewsCategory $category): bool
    {
        return $user->can('news.delete');
    }
}
