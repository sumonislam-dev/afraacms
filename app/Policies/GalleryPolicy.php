<?php

namespace App\Policies;

use App\Models\Gallery;
use App\Models\User;

class GalleryPolicy
{
    /**
     * Determine whether the user can view the list of albums.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('gallery.view');
    }

    /**
     * Determine whether the user can view the given album.
     */
    public function view(User $user, Gallery $gallery): bool
    {
        return $user->can('gallery.view');
    }

    /**
     * Determine whether the user can create albums.
     */
    public function create(User $user): bool
    {
        return $user->can('gallery.create');
    }

    /**
     * Determine whether the user can update the given album (including its items).
     */
    public function update(User $user, Gallery $gallery): bool
    {
        return $user->can('gallery.edit');
    }

    /**
     * Determine whether the user can delete the given album.
     */
    public function delete(User $user, Gallery $gallery): bool
    {
        return $user->can('gallery.delete');
    }
}
