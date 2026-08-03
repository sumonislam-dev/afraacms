<?php

namespace App\Http\Requests\Admin\Concerns;

use Illuminate\Validation\Rule;

/**
 * Validation rules for the shared "SEO" fields partial (admin._seo-fields),
 * reused by every content type's Store/Update request that has its own
 * SeoMeta override (Pages, Projects, Galleries).
 */
trait HasSeoRules
{
    /**
     * @return array<string, mixed>
     */
    protected function seoRules(): array
    {
        return [
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:255'],
            'seo_image' => ['nullable', 'integer', Rule::exists('media_items', 'id')],
            'seo_canonical' => ['nullable', 'string', 'max:2048'],
            'seo_robots' => ['nullable', Rule::in(['index, follow', 'noindex, follow', 'index, nofollow', 'noindex, nofollow'])],
        ];
    }
}
