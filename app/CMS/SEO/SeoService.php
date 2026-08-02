<?php

namespace App\CMS\SEO;

class SeoService
{
    /**
     * Resolve the meta tag data for the current page: site-wide defaults
     * from the SEO/General settings, overridden by whatever a specific page
     * passes in (e.g. a Page's own title/description in a later phase).
     *
     * @param  array<string, string|null>  $overrides
     * @return array{title: string, description: ?string, image: ?string, url: string, site_name: string}
     */
    public function resolve(array $overrides = []): array
    {
        $siteName = setting('site_name', config('app.name', 'AfraaCMS'));

        $defaults = [
            'title' => setting('meta_title') ?: $siteName,
            'description' => setting('meta_description'),
            'image' => media_url(setting('og_image')),
            'url' => url()->current(),
            'site_name' => $siteName,
        ];

        return array_merge($defaults, array_filter($overrides, fn ($value) => filled($value)));
    }
}
