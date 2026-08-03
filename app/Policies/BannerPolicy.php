<?php

namespace App\Policies;

use App\Models\Banner;
use App\Models\User;

class BannerPolicy
{
    /**
     * Determine whether the user can view the list of banners.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('banners.view');
    }

    /**
     * Determine whether the user can view the given banner.
     */
    public function view(User $user, Banner $banner): bool
    {
        return $user->can('banners.view');
    }

    /**
     * Determine whether the user can create banners.
     */
    public function create(User $user): bool
    {
        return $user->can('banners.create');
    }

    /**
     * Determine whether the user can update the given banner.
     */
    public function update(User $user, Banner $banner): bool
    {
        return $user->can('banners.edit');
    }

    /**
     * Determine whether the user can delete the given banner.
     */
    public function delete(User $user, Banner $banner): bool
    {
        return $user->can('banners.delete');
    }
}
