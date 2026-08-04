<?php

namespace App\Policies;

use App\Models\NewsPost;
use App\Models\User;

class NewsPostPolicy
{
    /**
     * Determine whether the user can view the list of posts.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('news.view');
    }

    /**
     * Determine whether the user can view the given post.
     */
    public function view(User $user, NewsPost $post): bool
    {
        return $user->can('news.view');
    }

    /**
     * Determine whether the user can create posts.
     */
    public function create(User $user): bool
    {
        return $user->can('news.create');
    }

    /**
     * Determine whether the user can update the given post.
     */
    public function update(User $user, NewsPost $post): bool
    {
        return $user->can('news.edit');
    }

    /**
     * Determine whether the user can delete the given post.
     */
    public function delete(User $user, NewsPost $post): bool
    {
        return $user->can('news.delete');
    }

    /**
     * Determine whether the user can restore the given trashed post.
     */
    public function restore(User $user, NewsPost $post): bool
    {
        return $user->can('news.delete');
    }

    /**
     * Determine whether the user can permanently delete the given trashed post.
     */
    public function forceDelete(User $user, NewsPost $post): bool
    {
        return $user->can('news.delete');
    }
}
