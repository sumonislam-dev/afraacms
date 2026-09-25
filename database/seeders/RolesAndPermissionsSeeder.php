<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Seed every permission, the Super Admin/Admin/Editor/Viewer roles, and
     * the initial Super Admin user.
     *
     * This is the ONLY place permissions and roles get seeded from - there
     * are deliberately no permission-seeding migrations, so re-run this
     * seeder (`php artisan db:seed --class=RolesAndPermissionsSeeder`)
     * wherever it's already deployed after adding a module/permission to
     * config/permission_modules.php.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $modules = config('permission_modules.modules', []);

        foreach ($modules as $module => $actions) {
            foreach ($actions as $action) {
                Permission::firstOrCreate(['name' => "{$module}.{$action}", 'guard_name' => 'web']);
            }
        }

        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $superAdmin->syncPermissions(Permission::all());

        foreach (config('permission_modules.roles', []) as $roleName => $definition) {
            $permissionNames = $this->resolveRolePermissions($modules, $definition);

            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions(Permission::whereIn('name', $permissionNames)->get());
        }

        $superAdminAccount = config('admin.super_admin');

        // The default account (config/admin.php) is a known, committed
        // password meant only for local development - refuse to seed it in
        // production so a forgotten SUPER_ADMIN_PASSWORD override can't
        // silently hand out a full-access account with a public password.
        if (app()->environment('production') && in_array($superAdminAccount['password'], [null, '', 'password'], true)) {
            throw new \RuntimeException(
                'Refusing to seed the Super Admin account in production with no SUPER_ADMIN_PASSWORD (or the default "password") set. '
                .'Set SUPER_ADMIN_EMAIL/SUPER_ADMIN_PASSWORD in the environment before running this seeder.'
            );
        }

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

    /**
     * Resolve a role's config definition (any combination of "modules",
     * "exclude_modules", "view_only_modules", "extra_permissions") into a
     * flat, deduped list of "module.action" permission names.
     *
     * @param  array<string, array<int, string>>  $modules
     * @param  array<string, mixed>  $definition
     * @return Collection<int, string>
     */
    private function resolveRolePermissions(array $modules, array $definition): Collection
    {
        $permissionNames = collect();

        if (isset($definition['modules'])) {
            $permissionNames = $permissionNames->merge(
                collect($definition['modules'])
                    ->flatMap(fn (string $module) => collect($modules[$module] ?? [])
                        ->map(fn (string $action) => "{$module}.{$action}"))
            );
        }

        if (isset($definition['exclude_modules'])) {
            $permissionNames = $permissionNames->merge(
                collect($modules)
                    ->except($definition['exclude_modules'])
                    ->flatMap(fn (array $actions, string $module) => collect($actions)
                        ->map(fn (string $action) => "{$module}.{$action}"))
            );
        }

        if (isset($definition['view_only_modules'])) {
            $permissionNames = $permissionNames->merge(
                collect($definition['view_only_modules'])
                    ->filter(fn (string $module) => in_array('view', $modules[$module] ?? [], true))
                    ->map(fn (string $module) => "{$module}.view")
            );
        }

        return $permissionNames
            ->merge($definition['extra_permissions'] ?? [])
            ->unique()
            ->values();
    }
}
