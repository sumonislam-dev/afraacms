<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Super Admin Seed Account
    |--------------------------------------------------------------------------
    |
    | Used by RolesAndPermissionsSeeder to create the initial Super Admin
    | user. Override these via the environment for every non-local install -
    | the defaults below are for local development only.
    |
    */

    'super_admin' => [
        'name' => env('SUPER_ADMIN_NAME', 'Super Admin'),
        'email' => env('SUPER_ADMIN_EMAIL', 'superadmin@afraacms.test'),
        'password' => env('SUPER_ADMIN_PASSWORD', 'password'),
    ],
];
