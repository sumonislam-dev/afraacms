<?php

/*
|--------------------------------------------------------------------------
| Dedicated SEO Screen Schema
|--------------------------------------------------------------------------
|
| Site-wide sitemap/robots configuration, edited on its own admin screen
| (permission "seo.*") rather than the generic Settings screen
| (permission "settings.*") - see config/settings.php's "seo" group for the
| more basic default meta title/description/og_image fields, and
| config/permissions.php's comment on why these are split.
|
| Backed by the same `settings` table/Setting model as config/settings.php
| (SettingsSeeder seeds both), so setting('robots_txt') etc. work exactly
| like any other setting - only the admin screen and permission differ.
|
*/

return [
    'fields' => [
        'default_robots' => [
            'label' => 'Default Meta Robots',
            'type' => 'select',
            'default' => 'index, follow',
            'description' => 'Used on any page/project/album that has no per-item Robots override.',
        ],
        'sitemap_include_projects' => ['label' => 'Include Projects in Sitemap', 'type' => 'boolean', 'default' => true],
        'sitemap_include_galleries' => ['label' => 'Include Galleries in Sitemap', 'type' => 'boolean', 'default' => true],
        'robots_txt' => [
            'label' => 'robots.txt Contents',
            'type' => 'textarea',
            'default' => "User-agent: *\nAllow: /",
            'description' => 'The sitemap reference is appended automatically.',
        ],
    ],
];
