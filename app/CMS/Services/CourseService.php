<?php

namespace App\CMS\Services;

use App\Models\Course;

class CourseService
{
    /**
     * Create a new course.
     */
    public function create(array $data): Course
    {
        return Course::create($data);
    }

    /**
     * Update an existing course.
     */
    public function update(Course $course, array $data): Course
    {
        $course->update($data);

        return $course;
    }

    /**
     * Delete a course.
     */
    public function delete(Course $course): void
    {
        $course->delete();
    }

    /**
     * Restore a soft-deleted course.
     */
    public function restore(Course $course): Course
    {
        $course->restore();

        return $course;
    }

    /**
     * Permanently delete a soft-deleted course.
     *
     * Refuses if any enrollment (including trashed ones) still references
     * this course - the database's restrictOnDelete foreign key would
     * reject it anyway, but checking here lets the controller show a
     * friendly message instead of a raw database error.
     */
    public function forceDelete(Course $course): bool
    {
        if ($course->enrollments()->withTrashed()->exists()) {
            return false;
        }

        $course->forceDelete();

        return true;
    }
}
