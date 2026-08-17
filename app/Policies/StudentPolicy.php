<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;

class StudentPolicy
{
    /**
     * Determine whether the user can view the list of students.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('students.view');
    }

    /**
     * Determine whether the user can view the given student.
     */
    public function view(User $user, Student $student): bool
    {
        return $user->can('students.view');
    }

    /**
     * Determine whether the user can create students.
     */
    public function create(User $user): bool
    {
        return $user->can('students.create');
    }

    /**
     * Determine whether the user can update the given student.
     */
    public function update(User $user, Student $student): bool
    {
        return $user->can('students.edit');
    }

    /**
     * Determine whether the user can delete the given student.
     */
    public function delete(User $user, Student $student): bool
    {
        return $user->can('students.delete');
    }

    /**
     * Determine whether the user can restore the given trashed student.
     */
    public function restore(User $user, Student $student): bool
    {
        return $user->can('students.delete');
    }

    /**
     * Determine whether the user can permanently delete the given trashed student.
     */
    public function forceDelete(User $user, Student $student): bool
    {
        return $user->can('students.delete');
    }
}
