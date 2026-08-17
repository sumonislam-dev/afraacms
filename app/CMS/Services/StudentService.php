<?php

namespace App\CMS\Services;

use App\Models\Student;

class StudentService
{
    /**
     * Create a new student.
     */
    public function create(array $data): Student
    {
        return Student::create($data);
    }

    /**
     * Update an existing student.
     */
    public function update(Student $student, array $data): Student
    {
        $student->update($data);

        return $student;
    }

    /**
     * Delete a student.
     */
    public function delete(Student $student): void
    {
        $student->delete();
    }

    /**
     * Restore a soft-deleted student.
     */
    public function restore(Student $student): Student
    {
        $student->restore();

        return $student;
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
