<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMenuItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('menu'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'label' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(['internal', 'external'])],
            'url' => ['required', 'string', 'max:2048'],
            'icon' => ['nullable', 'string', Rule::in(array_keys(config('icons', [])))],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
