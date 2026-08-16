<?php

/*
|--------------------------------------------------------------------------
| Section Types
|--------------------------------------------------------------------------
|
| The single source of truth for the Section Engine: which fields each
| section type shows on its own form (from the "sections" table's generic
| columns), whether it manages a repeatable list of section_items, and
| which fields those items show. The same key drives the frontend render
| dispatch (type "hero" -> resources/views/frontend/sections/hero.blade.php).
|
| Field keys map directly to sections/section_items table columns:
| heading, subheading, body, image, button_text, button_url, layout
| (sections) and title, subtitle, body, image, value, url, icon
| (section_items).
|
*/

return [
    'types' => [
        'hero' => [
            'label' => 'Hero',
            'fields' => ['heading', 'subheading', 'body', 'image', 'button_text', 'button_url'],
            'has_items' => false,
        ],
        'rich_text' => [
            'label' => 'Rich Text',
            'fields' => ['heading', 'body'],
            'has_items' => false,
        ],
        'cards' => [
            'label' => 'Cards',
            'fields' => ['heading', 'subheading'],
            'has_items' => true,
            'item_fields' => ['title', 'body', 'image', 'icon', 'url'],
        ],
        'gallery' => [
            'label' => 'Gallery',
            'fields' => ['heading', 'subheading'],
            'has_items' => true,
            'item_fields' => ['image', 'title'],
        ],
        'cta' => [
            'label' => 'Call to Action',
            'fields' => ['heading', 'subheading', 'button_text', 'button_url'],
            'has_items' => false,
        ],
        'faq' => [
            'label' => 'FAQ',
            'fields' => ['heading', 'subheading'],
            'has_items' => true,
            'item_fields' => ['title', 'body'],
        ],
        'timeline' => [
            'label' => 'Timeline',
            'fields' => ['heading', 'subheading'],
            'has_items' => true,
            'item_fields' => ['title', 'subtitle', 'body'],
        ],
        'stats' => [
            'label' => 'Stats',
            'fields' => ['heading', 'subheading'],
            'has_items' => true,
            'item_fields' => ['title', 'value', 'icon'],
        ],
        'image_text' => [
            'label' => 'Image + Text',
            'fields' => ['heading', 'subheading', 'body', 'image', 'layout'],
            'has_items' => false,
        ],
        'contact' => [
            'label' => 'Contact',
            'fields' => ['heading', 'subheading'],
            'has_items' => false,
        ],
        'projects' => [
            'label' => 'Projects',
            'fields' => ['heading', 'subheading', 'button_text', 'button_url'],
            'has_items' => false,
        ],
        'gallery_albums' => [
            'label' => 'Gallery Albums',
            'fields' => ['heading', 'subheading', 'button_text', 'button_url'],
            'has_items' => false,
        ],
        'news' => [
            'label' => 'Latest News',
            'fields' => ['heading', 'subheading', 'button_text', 'button_url'],
            'has_items' => false,
        ],
        'stories' => [
            'label' => 'Success Stories',
            'fields' => ['heading', 'subheading', 'button_text', 'button_url'],
            'has_items' => false,
        ],
        'photo_slider' => [
            'label' => 'Photo Slider',
            'fields' => ['heading', 'subheading'],
            'has_items' => false,
        ],
        'team' => [
            'label' => 'Team (Manual List)',
            'fields' => ['heading', 'subheading'],
            'has_items' => true,
            'item_fields' => ['title', 'subtitle', 'body', 'image', 'url'],
            'item_labels' => [
                'title' => 'Name',
                'subtitle' => 'Role / Position',
                'body' => 'Bio',
                'url' => 'Profile Link (optional, e.g. LinkedIn)',
            ],
        ],
        'team_members' => [
            'label' => 'Team (Global Directory)',
            'fields' => ['heading', 'subheading'],
            'has_items' => false,
        ],
        'featured_visitors' => [
            'label' => 'Featured Visitors',
            'fields' => ['heading', 'subheading'],
            'has_items' => false,
        ],
    ],
];
