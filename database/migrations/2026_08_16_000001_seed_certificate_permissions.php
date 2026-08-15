<?php

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * Grants the certificates.* permissions this feature needs, so a plain
     * `php artisan migrate` is enough for Super Admin/Editor to reach the
     * new admin section - without this, RolesAndPermissionsSeeder only picks
     * up the new config/permissions.php module if someone remembers to
     * re-run it by hand, which would 403 even Super Admin on a fresh deploy.
     */
    public function up(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (['view', 'create', 'edit', 'delete'] as $action) {
            Permission::firstOrCreate(['name' => "certificates.{$action}", 'guard_name' => 'web']);
        }

        $permissions = Permission::where('name', 'like', 'certificates.%')->get();

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
        Permission::where('name', 'like', 'certificates.%')->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
