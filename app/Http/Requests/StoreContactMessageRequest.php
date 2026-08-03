<?php

namespace App\Http\Requests;

use App\CMS\Services\ContactService;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class StoreContactMessageRequest extends FormRequest
{
    /**
     * Anyone may submit the contact form.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * "website" is an invisible honeypot field (see the frontend contact
     * section partial): a real visitor never sees or fills it, so any
     * value there means the submission came from a bot.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
            'website' => ['prohibited'],
        ];
    }

    /**
     * Configure the validator instance.
     *
     * reCAPTCHA is only checked when both keys are configured in Settings,
     * so the form works out of the box without requiring external setup.
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
