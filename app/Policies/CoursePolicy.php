<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;

class CoursePolicy
{
    /**
     * Determine whether the user can view the list of courses.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('courses.view');
    }

    /**
     * Determine whether the user can view the given course.
     */
    public function view(User $user, Course $course): bool
    {
        return $user->can('courses.view');
    }

    /**
     * Determine whether the user can create courses.
     */
    public function create(User $user): bool
    {
        return $user->can('courses.create');
    }

    /**
     * Determine whether the user can update the given course.
     */
    public function update(User $user, Course $course): bool
    {
        return $user->can('courses.edit');
    }

    /**
     * Determine whether the user can delete the given course.
     */
    public function delete(User $user, Course $course): bool
    {
        return $user->can('courses.delete');
    }

    /**
     * Determine whether the user can restore the given trashed course.
     */
    public function restore(User $user, Course $course): bool
    {
        return $user->can('courses.delete');
    }

    /**
     * Determine whether the user can permanently delete the given trashed course.
     */
    public function forceDelete(User $user, Course $course): bool
    {
        return $user->can('courses.delete');
    }
}
