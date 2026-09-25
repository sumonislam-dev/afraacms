<?php

namespace App\CMS\Imports;

use App\CMS\Imports\Concerns\NormalizesExcelDates;
use App\Models\Student;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

/**
 * Bulk student registration from an uploaded spreadsheet. Each row is saved
 * individually (not a batch insert) so Student's booted():creating hook
 * still runs and generates each row's student_code - see StudentsTemplateExport
 * for the exact column headers this expects.
 */
class StudentsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, SkipsEmptyRows
{
    use Importable, NormalizesExcelDates, SkipsFailures;

    /**
     * @param  array<string, mixed>  $row
     */
    public function model(array $row): Student
    {
        return new Student([
            'name' => $row['name'],
            'father_name' => $row['father_name'],
            'mother_name' => $row['mother_name'],
            'date_of_birth' => $this->normalizeDate($row['date_of_birth']),
            'phone' => $row['phone'] ?? null,
            'email' => $row['email'] ?? null,
            'address' => $row['address'] ?? null,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'father_name' => ['required', 'string', 'max:150'],
            'mother_name' => ['required', 'string', 'max:150'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
            'address' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function prepareForValidation(array $data, int $index): array
    {
        // Blank spreadsheet cells arrive as "", not null - left as-is, "" fails
        // rules like "email" even though the field is otherwise nullable.
        $data = array_map(fn ($value) => $value === '' ? null : $value, $data);

        // A purely-numeric-looking cell (e.g. phone "01700000000") gets
        // auto-detected as an int/float by the spreadsheet reader, which then
        // fails the "string" rule below - phone numbers aren't numbers.
        if (isset($data['phone'])) {
            $data['phone'] = (string) $data['phone'];
        }

        $data['date_of_birth'] = $this->normalizeDate($data['date_of_birth'] ?? null);

        return $data;
    }
}
