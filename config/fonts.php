<?php

/*
|--------------------------------------------------------------------------
| Frontend Theme Fonts
|--------------------------------------------------------------------------
|
| The curated list of fonts an admin can pick for the public site's
| heading (--font-display) and body (--font-body) typefaces, via the
| Settings > Branding screen. Deliberately a fixed list, not a free-text
| field: a typo here would silently break every page's typography.
|
| "google" is the family segment used in the Google Fonts CSS2 API URL
| (https://fonts.googleapis.com/css2?family=...). "family" is the CSS
| font-family stack, with a sane system-font fallback if the Google
| Fonts request ever fails to load.
|
*/

return [
    'heading' => [
        'merriweather' => [
            'label' => 'Merriweather (Serif, default)',
            'google' => 'Merriweather:ital,wght@0,300;0,400;0,700;0,900;1,400',
            'family' => "'Merriweather', ui-serif, Georgia, serif",
        ],
        'playfair-display' => [
            'label' => 'Playfair Display (Elegant Serif)',
            'google' => 'Playfair+Display:wght@400;600;700;800;900',
            'family' => "'Playfair Display', ui-serif, Georgia, serif",
        ],
        'poppins' => [
            'label' => 'Poppins (Modern Sans)',
            'google' => 'Poppins:wght@400;500;600;700;800',
            'family' => "'Poppins', ui-sans-serif, system-ui, sans-serif",
        ],
        'lora' => [
            'label' => 'Lora (Soft Serif)',
            'google' => 'Lora:ital,wght@0,400;0,500;0,600;0,700;1,400',
            'family' => "'Lora', ui-serif, Georgia, serif",
        ],
    ],

    'body' => [
        'inter' => [
            'label' => 'Inter (Sans, default)',
            'google' => 'Inter:wght@400;500;600;700;800',
            'family' => "'Inter', ui-sans-serif, system-ui, sans-serif",
        ],
        'nunito-sans' => [
            'label' => 'Nunito Sans (Friendly)',
            'google' => 'Nunito+Sans:wght@400;500;600;700;800',
            'family' => "'Nunito Sans', ui-sans-serif, system-ui, sans-serif",
        ],
        'open-sans' => [
            'label' => 'Open Sans (Neutral)',
            'google' => 'Open+Sans:wght@400;500;600;700;800',
            'family' => "'Open Sans', ui-sans-serif, system-ui, sans-serif",
        ],
        'source-sans-3' => [
            'label' => 'Source Sans 3 (Clean)',
            'google' => 'Source+Sans+3:wght@400;500;600;700;800',
            'family' => "'Source Sans 3', ui-sans-serif, system-ui, sans-serif",
        ],
    ],
];
