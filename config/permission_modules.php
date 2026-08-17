<?php

/*
|--------------------------------------------------------------------------
| Permission Modules
|--------------------------------------------------------------------------
|
| Each key is a module and its value is the list of CRUD-style actions
| available on that module. Permission names are generated as
| "{module}.{action}" (e.g. "pages.create") by the RolesAndPermissionsSeeder.
|
| Modules with only ['view', 'edit'] represent singleton/config-style
| screens (SEO) that have nothing to "create" or "delete".
| "Dashboard" only needs "view" since it isn't a manageable resource.
| Settings adds a third action, "developer", gating the handful of
| fields (config/settings.php field key "locked") that must stay
| Super-Admin-only even for an Admin/Editor who's been granted general
| settings.edit - see UpdateSettingsRequest/SettingController.
|
| Future modules only need to add an entry here and reference the
| resulting permission name from their routes/policies - the seeder
| and the admin sidebar will pick them up automatically. This is also
| the ONLY place permissions get seeded from - there are deliberately no
| migrations seeding permissions, so a new module/permission needs
| `php artisan db:seed --class=RolesAndPermissionsSeeder` re-run wherever
| it's already deployed (a plain `migrate` alone won't pick it up).
|
*/

return [
    'modules' => [
        'dashboard' => ['view'],
        'users' => ['view', 'create', 'edit', 'delete'],
        'roles' => ['view', 'create', 'edit', 'delete'],
        'permissions' => ['view'],
        'activity' => ['view'],
        'settings' => ['view', 'edit', 'developer'],
        'media' => ['view', 'create', 'edit', 'delete'],
        'menus' => ['view', 'create', 'edit', 'delete'],
        'pages' => ['view', 'create', 'edit', 'delete'],
        'sections' => ['view', 'create', 'edit', 'delete'],
        'banners' => ['view', 'create', 'edit', 'delete'],
        'projects' => ['view', 'create', 'edit', 'delete'],
        'gallery' => ['view', 'create', 'edit', 'delete'],
        'team' => ['view', 'create', 'edit', 'delete'],
        'news' => ['view', 'create', 'edit', 'delete'],
        'stories' => ['view', 'create', 'edit', 'delete'],
        'certificates' => ['view', 'create', 'edit', 'delete'],
        'donations' => ['view', 'create', 'edit', 'delete'],
        'visitor_book' => ['view', 'edit', 'delete'],
        'featured_visitors' => ['view', 'create', 'edit', 'delete'],
        'students' => ['view', 'create', 'edit', 'delete'],
        'courses' => ['view', 'create', 'edit', 'delete'],
        'enrollments' => ['view', 'create', 'edit', 'delete'],
        'contact' => ['view', 'edit', 'delete'],
        'seo' => ['view', 'edit'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Role Permission Grants
    |--------------------------------------------------------------------------
    |
    | "Super Admin" isn't listed here - RolesAndPermissionsSeeder always
    | grants it every permission that exists, directly.
    |
    | Every other role is defined by up to three keys, all optional and
    | combinable:
    | - "modules": an allow-list of module names granted every action
    |   listed for that module above (full CRUD on that module).
    | - "exclude_modules": the inverse - every module's full action set
    |   EXCEPT the ones listed. Used for "give this role everything except
    |   X" roles, so newly added modules are included automatically
    |   without this config needing an update.
    | - "view_only_modules": grants just the "view" action (if the module
    |   has one) for each listed module - read-only access.
    | - "extra_permissions": individual "module.action" names granted on
    |   top of the above, for a module where the role needs only a slice
    |   of its actions (e.g. settings.view/settings.edit but never the
    |   Super-Admin-only settings.developer).
    |
    */

    'roles' => [
        'Admin' => [
            // Everything except the settings.developer field (see below).
            // Admin can manage Users and Roles, but StoreRoleRequest/
            // UpdateRoleRequest only let a user grant permissions they
            // already hold, and UserPolicy/UpdateUserRequest block anyone
            // but a Super Admin from touching a Super Admin account or
            // role - so Admin can't clone or escalate into Super Admin.
            'exclude_modules' => ['settings'],
            'extra_permissions' => ['settings.view', 'settings.edit'],
        ],

        'Editor' => [
            // Day-to-day content management: every content-facing module,
            // none of the platform-level ones (Users, Roles, Permissions,
            // Activity, Settings beyond view/edit).
            'modules' => [
                'dashboard', 'media', 'menus', 'pages', 'sections', 'banners', 'projects', 'gallery',
                'team', 'news', 'stories', 'certificates', 'donations', 'visitor_book', 'featured_visitors',
                'students', 'courses', 'enrollments', 'contact', 'seo',
            ],
            'extra_permissions' => ['settings.view', 'settings.edit'],
        ],

        'Viewer' => [
            // Read-only on content modules. Deliberately excludes Users,
            // Roles, Permissions, Activity, and Settings - those expose
            // account/security-sensitive data a pure viewer shouldn't see.
            'view_only_modules' => [
                'dashboard', 'media', 'menus', 'pages', 'sections', 'banners', 'projects', 'gallery',
                'team', 'news', 'stories', 'certificates', 'donations', 'visitor_book', 'featured_visitors',
                'students', 'courses', 'enrollments', 'contact', 'seo',
            ],
        ],
    ],
];
