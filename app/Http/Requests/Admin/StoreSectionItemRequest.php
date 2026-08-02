<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSectionItemRequest extends FormRequest
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
            'title' => ['nullable', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'body' => ['nullable', 'string'],
            'image' => ['nullable', 'integer', Rule::exists('media_items', 'id')],
            'value' => ['nullable', 'string', 'max:255'],
            'url' => ['nullable', 'string', 'max:2048'],
            'icon' => ['nullable', 'string', Rule::in(array_keys(config('icons', [])))],
        ];
    }
}
