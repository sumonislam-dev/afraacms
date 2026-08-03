<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSeoRequest extends FormRequest
{
    /**
     * Authorization is handled by the "permission:seo.edit" route middleware.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'default_robots' => ['required', Rule::in(['index, follow', 'noindex, follow', 'index, nofollow', 'noindex, nofollow'])],
            'sitemap_include_projects' => ['sometimes', 'boolean'],
            'sitemap_include_galleries' => ['sometimes', 'boolean'],
            'robots_txt' => ['nullable', 'string'],
        ];
    }
}
