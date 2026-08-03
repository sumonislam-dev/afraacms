<?php

/*
|--------------------------------------------------------------------------
| Admin Sidebar Menu
|--------------------------------------------------------------------------
|
| Each item may define: label, icon, route, permission, and children (for
| nested menus). Items whose route is missing or not yet registered are
| rendered automatically as disabled "Soon" placeholders by the
| admin.sidebar-menu-item component, so future modules only need to
| register their route name here to go live in the sidebar.
|
| The optional "permission" key hides an item from the sidebar entirely
| for users who lack that permission (see admin.sidebar). A group with no
| visible children is hidden too, so navigation only ever shows what the
| current user can actually reach.
|
*/

return [
    [
        'label' => 'Dashboard',
        'icon' => 'squares-2x2',
        'route' => 'admin.dashboard',
        'permission' => 'dashboard.view',
    ],
    [
        'label' => 'Pages',
        'icon' => 'document-text',
        'route' => 'admin.pages.index',
        'permission' => 'pages.view',
    ],
    [
        'label' => 'Menu Builder',
        'icon' => 'bars-3',
        'route' => 'admin.menus.index',
        'permission' => 'menus.view',
    ],
    [
        'label' => 'Banners',
        'icon' => 'bars-4',
        'route' => 'admin.banners.index',
        'permission' => 'banners.view',
    ],
    [
        'label' => 'Media Library',
        'icon' => 'photo',
        'route' => 'admin.media.index',
        'permission' => 'media.view',
    ],
    [
        'label' => 'Galleries',
        'icon' => 'photo',
        'route' => 'admin.galleries.index',
        'permission' => 'gallery.view',
    ],
    [
        'label' => 'Projects',
        'icon' => 'chart-bar',
        'children' => [
            ['label' => 'All Projects', 'route' => 'admin.projects.index', 'permission' => 'projects.view'],
            ['label' => 'Categories', 'route' => 'admin.project-categories.index', 'permission' => 'projects.view'],
        ],
    ],
    [
        'label' => 'Inbox',
        'icon' => 'phone',
        'route' => 'admin.contact.index',
        'permission' => 'contact.view',
    ],
    [
        'label' => 'Access Control',
        'icon' => 'users',
        'children' => [
            ['label' => 'Users', 'route' => 'admin.users.index', 'permission' => 'users.view'],
            ['label' => 'Roles', 'route' => 'admin.roles.index', 'permission' => 'roles.view'],
            ['label' => 'Permissions', 'route' => 'admin.permissions.index', 'permission' => 'permissions.view'],
        ],
    ],
    [
        'label' => 'SEO',
        'icon' => 'globe-alt',
        'route' => 'admin.seo.edit',
        'permission' => 'seo.view',
    ],
    [
        'label' => 'Settings',
        'icon' => 'cog-6-tooth',
        'route' => 'admin.settings.edit',
        'permission' => 'settings.view',
    ],
];
