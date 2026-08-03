<?php

namespace Tests\Concerns;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

/**
 * Seeds the app's role/permission set and hands back a user in a given
 * role, so admin feature tests can assert both "allowed" and "denied"
 * behavior without re-deriving the permission map themselves.
 */
trait CreatesAdminUsers
{
    protected function superAdmin(): User
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('Super Admin');

        return $user;
    }

    protected function editor(): User
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('Editor');

        return $user;
    }

    protected function userWithoutPermissions(): User
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        return User::factory()->create();
    }
}
