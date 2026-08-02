<?php

namespace App\Policies;

use App\Models\MediaItem;
use App\Models\User;

class MediaItemPolicy
{
    /**
     * Determine whether the user can view the media library.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('media.view');
    }

    /**
     * Determine whether the user can view the given item.
     */
    public function view(User $user, MediaItem $mediaItem): bool
    {
        return $user->can('media.view');
    }

    /**
     * Determine whether the user can upload new media.
     */
    public function create(User $user): bool
    {
        return $user->can('media.create');
    }

    /**
     * Determine whether the user can rename or replace the given item.
     */
    public function update(User $user, MediaItem $mediaItem): bool
    {
        return $user->can('media.edit');
    }

    /**
     * Determine whether the user can delete the given item.
     */
    public function delete(User $user, MediaItem $mediaItem): bool
    {
        return $user->can('media.delete');
    }
}
