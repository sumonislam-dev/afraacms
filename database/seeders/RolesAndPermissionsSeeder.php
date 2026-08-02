<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Seed the roles, permissions, and the initial Super Admin user.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $modules = config('permissions.modules', []);

        foreach ($modules as $module => $actions) {
            foreach ($actions as $action) {
                Permission::firstOrCreate(['name' => "{$module}.{$action}", 'guard_name' => 'web']);
            }
        }

        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $superAdmin->syncPermissions(Permission::all());

        $editorPermissionNames = collect(config('permissions.editor_modules', []))
            ->flatMap(fn (string $module) => collect($modules[$module] ?? [])->map(fn ($action) => "{$module}.{$action}"))
            ->merge(config('permissions.editor_extra_permissions', []))
            ->unique();

        $editorPermissions = Permission::whereIn('name', $editorPermissionNames)->get();

        $editor = Role::firstOrCreate(['name' => 'Editor', 'guard_name' => 'web']);
        $editor->syncPermissions($editorPermissions);

        $superAdminAccount = config('admin.super_admin');

        $user = User::firstOrCreate(
            ['email' => $superAdminAccount['email']],
            [
                'name' => $superAdminAccount['name'],
                'password' => $superAdminAccount['password'],
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );

        $user->syncRoles([$superAdmin]);
    }
}
