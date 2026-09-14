<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Admin\Concerns\HasSeoRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStoryRequest extends FormRequest
{
    use HasSeoRules;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('story'));
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
            'category_id' => ['nullable', 'integer', Rule::exists('story_categories', 'id')],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('stories', 'slug')->ignore($this->route('story'))],
            'excerpt' => ['nullable', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'cover_image' => ['nullable', 'integer', Rule::exists('media_items', 'id')],
            'published_at' => ['nullable', 'date'],
            'is_featured' => ['sometimes', 'boolean'],
            'status' => ['required', Rule::in(['draft', 'published'])],
            'attachment' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:10240'],
            'remove_attachment' => ['sometimes', 'boolean'],
            ...$this->seoRules(),
        ];
    }
}
