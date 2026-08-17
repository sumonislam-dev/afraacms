<?php

namespace App\CMS\Services;

use App\CMS\Services\Concerns\PerformsBasicCrud;
use App\Models\Course;

class CourseService
{
    use PerformsBasicCrud;

    protected function modelClass(): string
    {
        return Course::class;
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
