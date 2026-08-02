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
| screens (Settings, SEO) that have nothing to "create" or "delete".
| "Dashboard" only needs "view" since it isn't a manageable resource.
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
        'settings' => ['view', 'edit'],
        'media' => ['view', 'create', 'edit', 'delete'],
        'menus' => ['view', 'create', 'edit', 'delete'],
        'pages' => ['view', 'create', 'edit', 'delete'],
        'sections' => ['view', 'create', 'edit', 'delete'],
        'projects' => ['view', 'create', 'edit', 'delete'],
        'gallery' => ['view', 'create', 'edit', 'delete'],
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
        'dashboard', 'media', 'menus', 'pages', 'sections', 'projects', 'gallery', 'contact', 'seo',
    ],
];
