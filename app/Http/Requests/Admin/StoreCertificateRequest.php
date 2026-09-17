<?php

namespace App\Http\Requests\Admin;

use App\Models\Certificate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCertificateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Certificate::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'project_id' => ['nullable', 'integer', Rule::exists('projects', 'id')],
            'enrollment_id' => ['nullable', 'integer', Rule::exists('enrollments', 'id'), Rule::unique('certificates', 'enrollment_id')],
            'recipient_name' => ['required_without:enrollment_id', 'nullable', 'string', 'max:255'],
            'program' => ['nullable', 'string', 'max:255'],
            'session' => ['nullable', 'string', 'max:255'],
            'grade' => ['nullable', 'string', 'max:255'],
            'completion_date' => ['nullable', 'date'],
            'roll_number' => ['nullable', 'string', 'max:255'],
            'registration_number' => ['nullable', 'string', 'max:255'],
            'issued_at' => ['required', 'date'],
            'status' => ['required', Rule::in(['valid', 'revoked'])],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
