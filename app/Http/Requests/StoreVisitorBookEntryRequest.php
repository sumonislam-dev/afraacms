<?php

namespace App\Http\Requests;

use App\CMS\Services\ContactService;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class StoreVisitorBookEntryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'visitor_name' => ['required', 'string', 'max:255'],
            'visitor_email' => ['nullable', 'email', 'max:255'],
            'opinion' => ['required', 'string', 'max:2000'],
            'website' => ['prohibited'],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if (! app(ContactService::class)->verifyRecaptcha($this->input('g-recaptcha-response'))) {
                $validator->errors()->add('g-recaptcha-response', __('Please complete the reCAPTCHA verification.'));
            }
        });
    }
}
