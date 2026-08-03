<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMenuItemRequest extends FormRequest
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
        $menuItem = $this->route('menuItem');

        return [
            'label' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(['internal', 'external'])],
            'url' => ['required', 'string', 'max:2048'],
            'open_in_new_tab' => ['sometimes', 'boolean'],
            'icon' => ['nullable', 'string', Rule::in(array_keys(config('icons', [])))],
            'is_active' => ['sometimes', 'boolean'],
            'parent_id' => [
                'nullable', 'integer',
                Rule::exists('menu_items', 'id')->where('menu_id', $this->route('menu')->id),
                Rule::notIn([$menuItem->id, ...$menuItem->descendantIds()]),
            ],
        ];
    }
}
