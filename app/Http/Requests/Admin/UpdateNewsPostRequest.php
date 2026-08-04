<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Admin\Concerns\HasSeoRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateNewsPostRequest extends FormRequest
{
    use HasSeoRules;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('post'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'category_id' => ['nullable', 'integer', Rule::exists('news_categories', 'id')],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('news_posts', 'slug')->ignore($this->route('post'))],
            'excerpt' => ['nullable', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'cover_image' => ['nullable', 'integer', Rule::exists('media_items', 'id')],
            'published_at' => ['nullable', 'date'],
            'is_featured' => ['sometimes', 'boolean'],
            'status' => ['required', Rule::in(['draft', 'published'])],
            ...$this->seoRules(),
        ];
    }
}
