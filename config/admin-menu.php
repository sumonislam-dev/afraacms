<?php

/*
|--------------------------------------------------------------------------
| Admin Sidebar Menu
|--------------------------------------------------------------------------
|
| Each item may define: label, icon, route, and children (for nested
| menus). Items whose route is missing or not yet registered are
| rendered automatically as disabled "Soon" placeholders by the
| admin.sidebar-menu-item component, so future modules only need to
| register their route name here to go live in the sidebar.
|
*/

return [
    [
        'label' => 'Dashboard',
        'icon' => 'squares-2x2',
        'route' => 'admin.dashboard',
    ],
    [
        'label' => 'Content',
        'icon' => 'document-text',
        'children' => [
            ['label' => 'Pages', 'route' => 'admin.pages.index'],
            ['label' => 'Sections', 'route' => 'admin.sections.index'],
        ],
    ],
    [
        'label' => 'Menu Builder',
        'icon' => 'bars-3',
        'route' => 'admin.menus.index',
    ],
    [
        'label' => 'Media Library',
        'icon' => 'photo',
        'route' => 'admin.media.index',
    ],
    [
        'label' => 'Users & Roles',
        'icon' => 'users',
        'children' => [
            ['label' => 'Users', 'route' => 'admin.users.index'],
            ['label' => 'Roles & Permissions', 'route' => 'admin.roles.index'],
        ],
    ],
    [
        'label' => 'SEO',
        'icon' => 'globe-alt',
        'route' => 'admin.seo.index',
    ],
    [
        'label' => 'Settings',
        'icon' => 'cog-6-tooth',
        'children' => [
            ['label' => 'General', 'route' => 'admin.settings.general'],
            ['label' => 'Social Links', 'route' => 'admin.settings.social'],
        ],
    ],
];
