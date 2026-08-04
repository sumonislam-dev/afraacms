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
| Super-Admin-only even for an Editor who's been granted general
| settings.edit - see UpdateSettingsRequest/SettingController.
|
| Future modules only need to add an entry here and reference the
| resulting permission name from their routes/policies - the seeder
| and the admin sidebar will pick them up automatically.
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
        'contact' => ['view', 'edit', 'delete'],
        'seo' => ['view', 'edit'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Editor Permissions
    |--------------------------------------------------------------------------
    |
    | The Editor role is meant for day-to-day content management. It is
    | granted every action on the content-facing modules below, but none
    | of the platform-level modules (Users, Roles, Permissions, Settings).
    |
    */

    'editor_modules' => [
        'dashboard', 'media', 'menus', 'pages', 'sections', 'banners', 'projects', 'gallery', 'team', 'contact', 'seo',
    ],

    /*
    |--------------------------------------------------------------------------
    | Additional Editor Permissions
    |--------------------------------------------------------------------------
    |
    | Individual permission names to grant Editor beyond the full-module
    | grants above, for modules where Editor should only get a subset of
    | actions (e.g. Editor can view and edit Settings, but never the
    | Super-Admin-only "developer" fields - deliberately NOT granting
    | settings.developer here).
    |
    */

    'editor_extra_permissions' => [
        'settings.view',
        'settings.edit',
    ],
];
