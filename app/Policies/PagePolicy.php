<?php

namespace App\Policies;

use App\Models\Page;
use App\Models\User;

class PagePolicy
{
    /**
     * Determine whether the user can view the list of pages.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('pages.view');
    }

    /**
     * Determine whether the user can view the given page.
     */
    public function view(User $user, Page $page): bool
    {
        return $user->can('pages.view');
    }

    /**
     * Determine whether the user can create pages.
     */
    public function create(User $user): bool
    {
        return $user->can('pages.create');
    }

    /**
     * Determine whether the user can update the given page.
     */
    public function update(User $user, Page $page): bool
    {
        return $user->can('pages.edit');
    }

    /**
     * Determine whether the user can delete the given page.
     */
    public function delete(User $user, Page $page): bool
    {
        return $user->can('pages.delete');
    }
}
