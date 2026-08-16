<?php

namespace App\Policies;

use App\Models\FeaturedVisitor;
use App\Models\User;

class FeaturedVisitorPolicy
{
    /**
     * Determine whether the user can view the list of featured visitors.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('featured_visitors.view');
    }

    /**
     * Determine whether the user can view the given featured visitor.
     */
    public function view(User $user, FeaturedVisitor $featuredVisitor): bool
    {
        return $user->can('featured_visitors.view');
    }

    /**
     * Determine whether the user can create featured visitors.
     */
    public function create(User $user): bool
    {
        return $user->can('featured_visitors.create');
    }

    /**
     * Determine whether the user can update the given featured visitor.
     */
    public function update(User $user, FeaturedVisitor $featuredVisitor): bool
    {
        return $user->can('featured_visitors.edit');
    }

    /**
     * Determine whether the user can delete the given featured visitor.
     */
    public function delete(User $user, FeaturedVisitor $featuredVisitor): bool
    {
        return $user->can('featured_visitors.delete');
    }

    /**
     * Determine whether the user can restore the given trashed featured visitor.
     */
    public function restore(User $user, FeaturedVisitor $featuredVisitor): bool
    {
        return $user->can('featured_visitors.delete');
    }

    /**
     * Determine whether the user can permanently delete the given trashed featured visitor.
     */
    public function forceDelete(User $user, FeaturedVisitor $featuredVisitor): bool
    {
        return $user->can('featured_visitors.delete');
    }
}
