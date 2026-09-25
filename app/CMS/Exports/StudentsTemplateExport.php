<?php

namespace App\CMS\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

/**
 * A blank template with one example row, matching exactly the columns
 * StudentsImport expects (WithHeadingRow normalizes these headers back down
 * to the same snake_case keys StudentsImport::rules() validates).
 */
class StudentsTemplateExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return ['Name', 'Father Name', 'Mother Name', 'Date of Birth', 'Phone', 'Email', 'Address'];
    }

    public function array(): array
    {
        return [
            ['Jane Doe', 'John Doe', 'Mary Doe', '2005-01-31', '01700000000', 'jane@example.com', 'Dhaka, Bangladesh'],
        ];
    }
}
