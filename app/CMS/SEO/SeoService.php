<?php

namespace App\CMS\SEO;

class SeoService
{
    /**
     * Resolve the meta tag data for the current page: site-wide defaults
     * from the Settings SEO group, overridden by whatever a specific page,
     * project, or gallery passes in (its own SeoMeta record, if any).
     *
     * "url" doubles as the canonical URL and the og:url/twitter value - real
     * SEO practice is for those to always agree, so a canonical override
     * is simply passed in as this same "url" key.
     *
     * @param  array<string, string|null>  $overrides
     * @return array{title: string, description: ?string, image: ?string, url: string, site_name: string, robots: string}
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
            'robots' => setting('default_robots') ?: 'index, follow',
        ];

        return array_merge($defaults, array_filter($overrides, fn ($value) => filled($value)));
    }
}
