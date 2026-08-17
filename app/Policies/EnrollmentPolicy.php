<?php

namespace App\Policies;

use App\Models\Enrollment;
use App\Models\User;

class EnrollmentPolicy
{
    /**
     * Determine whether the user can view the list of enrollments.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('enrollments.view');
    }

    /**
     * Determine whether the user can view the given enrollment.
     */
    public function view(User $user, Enrollment $enrollment): bool
    {
        return $user->can('enrollments.view');
    }

    /**
     * Determine whether the user can create enrollments.
     */
    public function create(User $user): bool
    {
        return $user->can('enrollments.create');
    }

    /**
     * Determine whether the user can update the given enrollment.
     */
    public function update(User $user, Enrollment $enrollment): bool
    {
        return $user->can('enrollments.edit');
    }

    /**
     * Determine whether the user can delete the given enrollment.
     */
    public function delete(User $user, Enrollment $enrollment): bool
    {
        return $user->can('enrollments.delete');
    }

    /**
     * Determine whether the user can restore the given trashed enrollment.
     */
    public function restore(User $user, Enrollment $enrollment): bool
    {
        return $user->can('enrollments.delete');
    }

    /**
     * Determine whether the user can permanently delete the given trashed enrollment.
     */
    public function forceDelete(User $user, Enrollment $enrollment): bool
    {
        return $user->can('enrollments.delete');
    }
}
