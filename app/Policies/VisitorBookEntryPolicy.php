<?php

namespace App\Policies;

use App\Models\User;
use App\Models\VisitorBookEntry;

class VisitorBookEntryPolicy
{
    /**
     * Determine whether the user can view the list of visitor book entries.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('visitor_book.view');
    }

    /**
     * Determine whether the user can view the given visitor book entry.
     */
    public function view(User $user, VisitorBookEntry $visitorBookEntry): bool
    {
        return $user->can('visitor_book.view');
    }

    /**
     * Determine whether the user can approve/reject the given entry.
     */
    public function update(User $user, VisitorBookEntry $visitorBookEntry): bool
    {
        return $user->can('visitor_book.edit');
    }

    /**
     * Determine whether the user can delete the given entry.
     */
    public function delete(User $user, VisitorBookEntry $visitorBookEntry): bool
    {
        return $user->can('visitor_book.delete');
    }
}
