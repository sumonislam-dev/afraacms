<?php

namespace App\Http\Requests\Admin;

use App\Models\Donation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDonationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Donation::class);
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
            'donor_name' => ['required', 'string', 'max:255'],
            'donor_email' => ['nullable', 'email', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'currency' => ['required', Rule::in(array_keys(config('donations.currencies')))],
            'method' => ['required', Rule::in(array_keys(config('donations.methods')))],
            'donated_at' => ['required', 'date'],
            'status' => ['required', Rule::in(['completed', 'refunded'])],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
