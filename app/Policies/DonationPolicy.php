<?php

namespace App\Policies;

use App\Models\Donation;
use App\Models\User;

class DonationPolicy
{
    /**
     * Determine whether the user can view the list of donations.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('donations.view');
    }

    /**
     * Determine whether the user can view the given donation.
     */
    public function view(User $user, Donation $donation): bool
    {
        return $user->can('donations.view');
    }

    /**
     * Determine whether the user can create donations.
     */
    public function create(User $user): bool
    {
        return $user->can('donations.create');
    }

    /**
     * Determine whether the user can update the given donation.
     */
    public function update(User $user, Donation $donation): bool
    {
        return $user->can('donations.edit');
    }

    /**
     * Determine whether the user can delete the given donation.
     */
    public function delete(User $user, Donation $donation): bool
    {
        return $user->can('donations.delete');
    }

    /**
     * Determine whether the user can restore the given trashed donation.
     */
    public function restore(User $user, Donation $donation): bool
    {
        return $user->can('donations.delete');
    }

    /**
     * Determine whether the user can permanently delete the given trashed donation.
     */
    public function forceDelete(User $user, Donation $donation): bool
    {
        return $user->can('donations.delete');
    }
}
