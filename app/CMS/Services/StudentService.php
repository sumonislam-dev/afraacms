<?php

namespace App\CMS\Services;

use App\CMS\Services\Concerns\PerformsBasicCrud;
use App\Models\Student;

class StudentService
{
    use PerformsBasicCrud;

    protected function modelClass(): string
    {
        return Student::class;
    }

    /**
     * Permanently delete a soft-deleted student.
     *
     * Refuses if any enrollment (including trashed ones) still references
     * this student - the database's restrictOnDelete foreign key would
     * reject it anyway, but checking here lets the controller show a
     * friendly message instead of a raw database error.
     */
    public function forceDelete(Student $student): bool
    {
        if ($student->enrollments()->withTrashed()->exists()) {
            return false;
        }

        $student->forceDelete();

        return true;
    }
}
