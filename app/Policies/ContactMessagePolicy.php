<?php

namespace App\Policies;

use App\Models\ContactMessage;
use App\Models\User;

class ContactMessagePolicy
{
    /**
     * Determine whether the user can view the inbox.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('contact.view');
    }

    /**
     * Determine whether the user can view the given message.
     */
    public function view(User $user, ContactMessage $contactMessage): bool
    {
        return $user->can('contact.view');
    }

    /**
     * Determine whether the user can mark the given message as read.
     */
    public function update(User $user, ContactMessage $contactMessage): bool
    {
        return $user->can('contact.edit');
    }

    /**
     * Determine whether the user can delete the given message.
     */
    public function delete(User $user, ContactMessage $contactMessage): bool
    {
        return $user->can('contact.delete');
    }
}
