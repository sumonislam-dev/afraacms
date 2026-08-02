<?php

namespace App\Http\Requests\Admin;

use App\Models\Page;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Page::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'required', 'string', 'max:255', 'alpha_dash',
                Rule::unique('pages', 'slug'),
                Rule::notIn(config('pages.reserved_slugs', [])),
            ],
            'status' => ['required', Rule::in(['draft', 'published'])],
            'template' => ['required', Rule::in(array_keys(config('pages.templates', [])))],
            'published_at' => ['nullable', 'date'],
            'content' => ['nullable', 'string'],
        ];
    }
}
