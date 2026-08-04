<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSectionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('section'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', Rule::in(array_keys(config('sections.types', [])))],
            'anchor' => ['nullable', 'string', 'max:100', 'alpha_dash'],
            'heading' => ['nullable', 'string', 'max:255'],
            'subheading' => ['nullable', 'string', 'max:255'],
            'body' => ['nullable', 'string'],
            'image' => ['nullable', 'integer', Rule::exists('media_items', 'id')],
            'button_text' => ['nullable', 'string', 'max:255'],
            'button_url' => ['nullable', 'string', 'max:2048'],
            'layout' => ['nullable', Rule::in(['image-left', 'image-right'])],
            'is_active' => ['sometimes', 'boolean'],
            'galleries' => ['sometimes', 'array'],
            'galleries.*' => ['integer', Rule::exists('galleries', 'id')],
        ];
    }
}
