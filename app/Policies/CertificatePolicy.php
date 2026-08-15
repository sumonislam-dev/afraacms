<?php

namespace App\Policies;

use App\Models\Certificate;
use App\Models\User;

class CertificatePolicy
{
    /**
     * Determine whether the user can view the list of certificates.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('certificates.view');
    }

    /**
     * Determine whether the user can view the given certificate.
     */
    public function view(User $user, Certificate $certificate): bool
    {
        return $user->can('certificates.view');
    }

    /**
     * Determine whether the user can create certificates.
     */
    public function create(User $user): bool
    {
        return $user->can('certificates.create');
    }

    /**
     * Determine whether the user can update the given certificate.
     */
    public function update(User $user, Certificate $certificate): bool
    {
        return $user->can('certificates.edit');
    }

    /**
     * Determine whether the user can delete the given certificate.
     */
    public function delete(User $user, Certificate $certificate): bool
    {
        return $user->can('certificates.delete');
    }

    /**
     * Determine whether the user can restore the given trashed certificate.
     */
    public function restore(User $user, Certificate $certificate): bool
    {
        return $user->can('certificates.delete');
    }

    /**
     * Determine whether the user can permanently delete the given trashed certificate.
     */
    public function forceDelete(User $user, Certificate $certificate): bool
    {
        return $user->can('certificates.delete');
    }
}
