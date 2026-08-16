<?php

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * Grants the featured_visitors.* permissions this feature needs, so a
     * plain `php artisan migrate` is enough for Super Admin/Editor to reach
     * the new admin section - see the identical certificates/donations
     * /visitor_book permission migrations for why this can't just rely on
     * RolesAndPermissionsSeeder being re-run by hand.
     */
    public function up(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (['view', 'create', 'edit', 'delete'] as $action) {
            Permission::firstOrCreate(['name' => "featured_visitors.{$action}", 'guard_name' => 'web']);
        }

        $permissions = Permission::where('name', 'like', 'featured_visitors.%')->get();

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
        Permission::where('name', 'like', 'featured_visitors.%')->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
