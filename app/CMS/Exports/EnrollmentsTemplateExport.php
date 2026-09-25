<?php

namespace App\CMS\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

/**
 * A blank template with one example row, matching exactly the columns
 * EnrollmentsImport expects. student_code/course_code are the same codes
 * shown on the Students/Courses admin lists - matching by name would be
 * ambiguous, these aren't.
 */
class EnrollmentsTemplateExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return [
            'Student Code', 'Course Code', 'Session', 'Roll Number', 'Registration Number',
            'Admission Date', 'Completion Date', 'Grade', 'Grade Point', 'Grade Scale', 'Result Status',
        ];
    }

    public function array(): array
    {
        return [
            ['STU-2026-00001', 'CRS-00001', '2025-2026', '101', 'REG-2025-001', '2025-01-15', '', '', '', '', 'pending'],
        ];
    }
}
