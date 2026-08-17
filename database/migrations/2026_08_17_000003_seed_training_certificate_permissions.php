<?php

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * Grants the students, courses, and enrollments permissions this feature
     * needs, so a plain `php artisan migrate` is enough for Super Admin/Editor
     * to reach the new admin sections - without this, RolesAndPermissionsSeeder
     * only picks up new config/permissions.php modules if someone remembers to
     * re-run it by hand, which would 403 even Super Admin on a fresh deploy.
     */
    public function up(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $modules = ['students', 'courses', 'enrollments'];

        foreach ($modules as $module) {
            foreach (['view', 'create', 'edit', 'delete'] as $action) {
                Permission::firstOrCreate(['name' => "{$module}.{$action}", 'guard_name' => 'web']);
            }
        }

        $permissions = Permission::where(function ($query) use ($modules) {
            foreach ($modules as $module) {
                $query->orWhere('name', 'like', "{$module}.%");
            }
        })->get();

        if ($superAdmin = Role::where('name', 'Super Admin')->first()) {
            $superAdmin->givePermissionTo($permissions);
        }

        if ($editor = Role::where('name', 'Editor')->first()) {
            $editor->givePermissionTo($permissions);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Permission::where(function ($query) {
            foreach (['students', 'courses', 'enrollments'] as $module) {
                $query->orWhere('name', 'like', "{$module}.%");
            }
        })->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
