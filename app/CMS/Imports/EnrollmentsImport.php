<?php

namespace App\CMS\Imports;

use App\CMS\Imports\Concerns\NormalizesExcelDates;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Student;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

/**
 * Bulk enrollment from an uploaded spreadsheet. Students and courses are
 * matched by their existing, already-visible codes (student_code,
 * course_code) rather than by name - names collide, codes don't. See
 * EnrollmentsTemplateExport for the exact column headers this expects.
 */
class EnrollmentsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, SkipsEmptyRows
{
    use Importable, NormalizesExcelDates, SkipsFailures;

    /**
     * Captured from the row currently being validated, in prepareForValidation()
     * - rules() itself gets no row argument, so this is how the roll_number
     * uniqueness rule below reaches the sibling course_code/session values.
     */
    private ?string $currentCourseCode = null;

    private ?string $currentSession = null;

    /**
     * @param  array<string, mixed>  $row
     */
    public function model(array $row): Enrollment
    {
        return new Enrollment([
            'student_id' => Student::where('student_code', $row['student_code'])->value('id'),
            'course_id' => Course::where('course_code', $row['course_code'])->value('id'),
            'session' => $row['session'],
            'roll_number' => $row['roll_number'] ?? null,
            'registration_number' => $row['registration_number'] ?? null,
            'admission_date' => $this->normalizeDate($row['admission_date'] ?? null),
            'completion_date' => $this->normalizeDate($row['completion_date'] ?? null),
            'grade' => $row['grade'] ?? null,
            'grade_point' => $row['grade_point'] ?? null,
            'grade_scale' => $row['grade_scale'] ?? null,
            'result_status' => $row['result_status'] ?? 'pending',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'student_code' => ['required', Rule::exists('students', 'student_code')],
            'course_code' => ['required', Rule::exists('courses', 'course_code')],
            'session' => ['required', 'string', 'max:20'],
            'roll_number' => [
                'nullable', 'string', 'max:50',
                Rule::unique('enrollments')->where(function ($query) {
                    $courseId = Course::where('course_code', $this->currentCourseCode)->value('id');

                    return $query->where('course_id', $courseId)->where('session', $this->currentSession);
                }),
            ],
            'registration_number' => ['nullable', 'string', 'max:50'],
            'admission_date' => ['nullable', 'date'],
            'completion_date' => ['nullable', 'date'],
            'grade' => ['nullable', 'string', 'max:10'],
            'grade_point' => ['nullable', 'numeric', 'min:0', 'max:99.99'],
            'grade_scale' => ['nullable', 'numeric', 'min:0', 'max:99.99'],
            'result_status' => ['nullable', Rule::in(['pending', 'passed', 'failed'])],
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function prepareForValidation(array $data, int $index): array
    {
        // Blank spreadsheet cells arrive as "", not null - left as-is, "" fails
        // rules like "numeric" even though the field is otherwise nullable.
        $data = array_map(fn ($value) => $value === '' ? null : $value, $data);

        // A purely-numeric-looking code cell (e.g. roll number "101") gets
        // auto-detected as an int/float by the spreadsheet reader, which then
        // fails the "string" rule below - these are codes, not numbers.
        foreach (['student_code', 'course_code', 'roll_number', 'registration_number', 'grade'] as $field) {
            if (isset($data[$field])) {
                $data[$field] = (string) $data[$field];
            }
        }

        $data['admission_date'] = $this->normalizeDate($data['admission_date'] ?? null);
        $data['completion_date'] = $this->normalizeDate($data['completion_date'] ?? null);

        $this->currentCourseCode = $data['course_code'] ?? null;
        $this->currentSession = $data['session'] ?? null;

        return $data;
    }
}
