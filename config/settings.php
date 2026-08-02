<?php

/*
|--------------------------------------------------------------------------
| Settings Schema
|--------------------------------------------------------------------------
|
| This is the single source of truth for every setting the admin panel
| exposes: SettingsSeeder creates the default rows from it, the settings
| edit form renders its fields and tabs from it, and UpdateSettingsRequest
| builds its validation rules from each field's "type".
|
| Supported types: text, textarea, email, number, url, boolean, image,
| color, select, password.
|
| A "select" field may set 'options' => 'timezones' to have the controller
| resolve PHP's timezone list at request time instead of hardcoding it here.
|
*/

return [
    'groups' => [
        'general' => [
            'label' => 'General',
            'icon' => 'squares-2x2',
            'fields' => [
                'site_name' => ['label' => 'Site Name', 'type' => 'text', 'default' => 'AfraaCMS'],
                'tagline' => ['label' => 'Tagline', 'type' => 'text', 'default' => ''],
                'timezone' => ['label' => 'Timezone', 'type' => 'select', 'options' => 'timezones', 'default' => 'UTC'],
            ],
        ],

        'branding' => [
            'label' => 'Branding',
            'icon' => 'photo',
            'fields' => [
                'logo' => ['label' => 'Logo', 'type' => 'image', 'default' => ''],
                'dark_logo' => ['label' => 'Dark Logo', 'type' => 'image', 'default' => '', 'description' => 'Used on dark backgrounds.'],
                'favicon' => ['label' => 'Favicon', 'type' => 'image', 'default' => ''],
                'brand_color' => ['label' => 'Brand Color', 'type' => 'color', 'default' => '#4f46e5'],
            ],
        ],

        'contact' => [
            'label' => 'Contact',
            'icon' => 'phone',
            'fields' => [
                'contact_email' => ['label' => 'Email', 'type' => 'email', 'default' => ''],
                'contact_phone' => ['label' => 'Phone', 'type' => 'text', 'default' => ''],
                'contact_address' => ['label' => 'Address', 'type' => 'textarea', 'default' => ''],
                'google_map' => ['label' => 'Google Map Embed URL', 'type' => 'url', 'default' => ''],
            ],
        ],

        'social' => [
            'label' => 'Social',
            'icon' => 'globe-alt',
            'fields' => [
                'facebook' => ['label' => 'Facebook', 'type' => 'url', 'default' => ''],
                'linkedin' => ['label' => 'LinkedIn', 'type' => 'url', 'default' => ''],
                'youtube' => ['label' => 'YouTube', 'type' => 'url', 'default' => ''],
                'instagram' => ['label' => 'Instagram', 'type' => 'url', 'default' => ''],
                'twitter' => ['label' => 'Twitter / X', 'type' => 'url', 'default' => ''],
            ],
        ],

        'footer' => [
            'label' => 'Footer',
            'icon' => 'document-text',
            'fields' => [
                'copyright' => ['label' => 'Copyright', 'type' => 'text', 'default' => ''],
                'footer_text' => ['label' => 'Footer Text', 'type' => 'textarea', 'default' => ''],
            ],
        ],

        'seo' => [
            'label' => 'SEO',
            'icon' => 'globe-alt',
            'fields' => [
                'meta_title' => ['label' => 'Default Meta Title', 'type' => 'text', 'default' => ''],
                'meta_description' => ['label' => 'Default Meta Description', 'type' => 'textarea', 'default' => ''],
                'og_image' => ['label' => 'Default OG Image', 'type' => 'image', 'default' => ''],
            ],
        ],

        'analytics' => [
            'label' => 'Analytics',
            'icon' => 'chart-bar',
            'fields' => [
                'google_analytics' => ['label' => 'Google Analytics ID', 'type' => 'text', 'default' => ''],
                'google_tag_manager' => ['label' => 'Google Tag Manager ID', 'type' => 'text', 'default' => ''],
                'facebook_pixel' => ['label' => 'Facebook Pixel ID', 'type' => 'text', 'default' => ''],
            ],
        ],

        'system' => [
            'label' => 'System',
            'icon' => 'cog-6-tooth',
            'fields' => [
                'maintenance_mode' => ['label' => 'Maintenance Mode', 'type' => 'boolean', 'default' => false],
                'registration_enabled' => ['label' => 'Registration Enabled', 'type' => 'boolean', 'default' => true],
                'items_per_page' => ['label' => 'Items Per Page', 'type' => 'number', 'default' => 15],
                'recaptcha_secret' => ['label' => 'reCAPTCHA Secret Key', 'type' => 'password', 'default' => ''],
            ],
        ],
    ],
];
