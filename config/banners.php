<?php

/*
|--------------------------------------------------------------------------
| Banner Types
|--------------------------------------------------------------------------
|
| Each key is a placement the frontend renders a banner into. Multiple
| Banner rows may exist per type (e.g. to schedule a hand-off between one
| promotion and the next via starts_at/ends_at), but only the
| highest-priority currently-active one is shown at each placement - see
| BannerService::current().
|
*/

return [
    'types' => [
        'homepage' => ['label' => 'Homepage Banner', 'description' => 'Shown at the top of the homepage, above all sections.'],
        'page' => ['label' => 'Page Banner', 'description' => 'Shown at the top of every inner page.'],
        'cta' => ['label' => 'CTA Banner', 'description' => 'Shown near the bottom of every page, site-wide.'],
        'popup' => ['label' => 'Popup Banner', 'description' => 'Shown as a dismissible popup; how often and where is set per banner.'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Popup Options
    |--------------------------------------------------------------------------
    |
    | How often a dismissed popup comes back, and which pages it shows on.
    | The first key of each list is the default.
    |
    */

    'popup_frequencies' => [
        'session' => 'Once per browser session',
        'always' => 'Every page load',
        'daily' => 'Once per day',
        'once' => 'Only once',
    ],

    'popup_pages' => [
        'all' => 'All pages',
        'home' => 'Homepage only',
    ],

    'popup_max_delay' => 60,
];
