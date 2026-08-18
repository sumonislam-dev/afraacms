<?php

namespace App\Http\Requests\Admin;

use App\Models\Section;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSectionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Section::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * All content fields are nullable: a section builder should let an
     * admin save a block before every field is filled in, rather than
     * enforcing a fixed checklist per type.
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
            'layout' => ['nullable', Rule::in(['image-left', 'image-right', 'light', 'dark'])],
            'is_active' => ['sometimes', 'boolean'],
            'galleries' => ['sometimes', 'array'],
            'galleries.*' => ['integer', Rule::exists('galleries', 'id')],
            'team_members' => ['sometimes', 'array'],
            'team_members.*' => ['integer', Rule::exists('team_members', 'id')],
            'team_category_ids' => ['sometimes', 'array'],
            'team_category_ids.*' => ['integer', Rule::exists('team_categories', 'id')],
        ];
    }
}
