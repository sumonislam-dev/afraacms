<?php

/*
|--------------------------------------------------------------------------
| Page Templates
|--------------------------------------------------------------------------
|
| Each key maps to a Blade view under resources/views/frontend/templates/
| and is the single source of truth for both the admin "Template" select
| and PageController's public-facing render lookup.
|
*/

return [
    'templates' => [
        'default' => 'Default',
        'full-width' => 'Full Width',
    ],

    /*
    |--------------------------------------------------------------------------
    | Reserved Slugs
    |--------------------------------------------------------------------------
    |
    | The public page route is a catch-all "/{slug}" registered after every
    | other top-level route, so these never actually collide - but letting
    | someone save a page under one of these slugs would make it permanently
    | unreachable with no explanation, so it's rejected at validation time.
    |
    | "gallery", "projects", "news" and "stories" are deliberately NOT
    | reserved: each has its own dedicated controller/route that always
    | takes priority over this catch-all, but the Page record living at
    | that slug is still real and editable - it supplies that listing
    | page's banner image/eyebrow/SEO override (see NewsController etc.).
    |
    */

    'reserved_slugs' => [
        'admin', 'login', 'register', 'logout', 'password',
        'profile', 'dashboard', 'verify-email', 'confirm-password',
        'up', 'storage',
    ],
];
