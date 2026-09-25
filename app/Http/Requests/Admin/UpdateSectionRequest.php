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
            'subheading' => ['nullable', 'string', 'max:1000'],
            'body' => ['nullable', 'string'],
            'image' => ['nullable', 'integer', Rule::exists('media_items', 'id')],
            'button_text' => ['nullable', 'string', 'max:255'],
            'button_url' => ['nullable', 'string', 'max:2048'],
            'layout' => ['nullable', Rule::in(['image-left', 'image-right', 'light', 'dark', 'cards', 'table'])],
            'show_search' => ['sometimes', 'boolean'],
            'item_limit' => ['nullable', 'integer', 'min:1', 'max:50'],
            'display_mode' => ['nullable', Rule::in(['preview', 'paginate'])],
            'is_active' => ['sometimes', 'boolean'],
            'galleries' => ['sometimes', 'array'],
            'galleries.*' => ['integer', Rule::exists('galleries', 'id')],
            'team_members' => ['sometimes', 'array'],
            'team_members.*' => ['integer', Rule::exists('team_members', 'id')],
            'team_category_ids' => ['sometimes', 'array'],
            'team_category_ids.*' => ['integer', Rule::exists('team_categories', 'id')],
            'source' => ['nullable', Rule::in(array_keys(config('content_sources', [])))],
            'content_category_ids' => ['sometimes', 'array'],
            'content_category_ids.*' => ['integer', $this->contentCategoryExistsRule()],
            'content_item_ids' => ['sometimes', 'array'],
            'content_item_ids.*' => ['integer', $this->contentItemExistsRule()],
        ];
    }

    /**
     * Which model the picked "content_category_ids" must exist in depends on
     * the submitted "source" - e.g. project category ids for source
     * "projects", news category ids for source "news"/"notices".
     */
    private function contentCategoryExistsRule(): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail): void {
            $model = config("content_sources.{$this->input('source')}.category_model");

            if ($model && ! $model::whereKey($value)->exists()) {
                $fail(__('The selected category is invalid.'));
            }
        };
    }

    /**
     * Which model the picked "content_item_ids" must exist in depends on the
     * submitted "source" - e.g. Project ids for source "projects".
     */
    private function contentItemExistsRule(): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail): void {
            $model = config("content_sources.{$this->input('source')}.item_model");

            if ($model && ! $model::whereKey($value)->exists()) {
                $fail(__('The selected item is invalid.'));
            }
        };
    }
}
