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
        'icon' => 'circle',
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
        'icon' => 'play',
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
        'label' => 'Team',
        'icon' => 'user-circle',
        'children' => [
            ['label' => 'All Members', 'route' => 'admin.team.index', 'permission' => 'team.view'],
            ['label' => 'Categories', 'route' => 'admin.team-categories.index', 'permission' => 'team.view'],
        ],
    ],
    [
        'label' => 'News',
        'icon' => 'information-circle',
        'children' => [
            ['label' => 'All Posts', 'route' => 'admin.news.index', 'permission' => 'news.view'],
            ['label' => 'Categories', 'route' => 'admin.news-categories.index', 'permission' => 'news.view'],
        ],
    ],
    [
        'label' => 'Success Stories',
        'icon' => 'check-circle',
        'route' => 'admin.stories.index',
        'permission' => 'stories.view',
    ],
    [
        'label' => 'Certificates',
        'icon' => 'shield-check',
        'route' => 'admin.certificates.index',
        'permission' => 'certificates.view',
    ],
    [
        'label' => 'Donations',
        'icon' => 'currency-dollar',
        'route' => 'admin.donations.index',
        'permission' => 'donations.view',
    ],
    [
        'label' => 'Featured Visitors',
        'icon' => 'arrow-top-right-on-square',
        'route' => 'admin.featured-visitors.index',
        'permission' => 'featured_visitors.view',
    ],
    [
        'label' => 'Visitor Book',
        'icon' => 'eye',
        'route' => 'admin.visitor-book.index',
        'permission' => 'visitor_book.view',
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
            ['label' => 'Activity Log', 'route' => 'admin.activity.index', 'permission' => 'activity.view'],
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
