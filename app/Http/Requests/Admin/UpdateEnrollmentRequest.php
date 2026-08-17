<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEnrollmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('enrollment'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'student_id' => ['required', Rule::exists('students', 'id')],
            'course_id' => ['required', Rule::exists('courses', 'id')],
            'session' => ['required', 'string', 'max:20'],
            'roll_number' => [
                'nullable', 'string', 'max:50',
                // $this->input(), not the $this->session/$this->course_id magic
                // getters - "session" collides with Request's own protected
                // $session property (the session store), so the property
                // accessor silently returns that instead of the form input.
                Rule::unique('enrollments')->ignore($this->route('enrollment'))->where(fn ($query) => $query
                    ->where('course_id', $this->input('course_id'))
                    ->where('session', $this->input('session'))),
            ],
            'registration_number' => ['nullable', 'string', 'max:50'],
            'admission_date' => ['nullable', 'date'],
            'completion_date' => ['nullable', 'date'],
            'grade' => ['nullable', 'string', 'max:10'],
            'grade_point' => ['nullable', 'numeric', 'min:0', 'max:99.99'],
            'grade_scale' => ['nullable', 'numeric', 'min:0', 'max:99.99'],
            'result_status' => ['required', Rule::in(['pending', 'passed', 'failed'])],
        ];
    }
}
