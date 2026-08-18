<?php

namespace Tests\Feature\Admin;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesAdminUsers;
use Tests\TestCase;

class RoleManagementTest extends TestCase
{
    use CreatesAdminUsers, RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('admin.roles.index'))->assertRedirect(route('login'));
    }

    public function test_editor_cannot_manage_roles(): void
    {
        $editor = $this->editor();

        $this->actingAs($editor)->get(route('admin.roles.index'))->assertForbidden();
        $this->actingAs($editor)->get(route('admin.roles.create'))->assertForbidden();
    }

    public function test_super_admin_can_create_a_role_with_permissions(): void
    {
        $superAdmin = $this->superAdmin();

        $response = $this->actingAs($superAdmin)->post(route('admin.roles.store'), [
            'name' => 'Content Reviewer',
            'permissions' => ['pages.view', 'pages.edit'],
        ]);

        $response->assertRedirect(route('admin.roles.index'));
        $role = Role::where('name', 'Content Reviewer')->firstOrFail();
        $this->assertTrue($role->hasPermissionTo('pages.view'));
        $this->assertTrue($role->hasPermissionTo('pages.edit'));
        $this->assertFalse($role->hasPermissionTo('pages.delete'));
    }

    public function test_super_admin_can_update_a_roles_permissions(): void
    {
        $superAdmin = $this->superAdmin();
        $role = Role::create(['name' => 'Content Reviewer', 'guard_name' => 'web']);
        $role->givePermissionTo('pages.view');

        $response = $this->actingAs($superAdmin)->put(route('admin.roles.update', $role), [
            'name' => 'Content Reviewer',
            'permissions' => ['pages.view', 'pages.delete'],
        ]);

        $response->assertRedirect(route('admin.roles.index'));
        $this->assertTrue($role->fresh()->hasPermissionTo('pages.delete'));
    }

    public function test_the_super_admin_role_cannot_be_deleted(): void
    {
        $superAdmin = $this->superAdmin();
        $superAdminRole = Role::where('name', 'Super Admin')->firstOrFail();

        $this->actingAs($superAdmin)
            ->delete(route('admin.roles.destroy', $superAdminRole))
            ->assertForbidden();

        $this->assertModelExists($superAdminRole);
    }

    public function test_a_role_with_assigned_users_cannot_be_deleted(): void
    {
        $superAdmin = $this->superAdmin();
        $role = Role::create(['name' => 'Content Reviewer', 'guard_name' => 'web']);
        $user = User::factory()->create();
        $user->assignRole('Content Reviewer');

        $this->actingAs($superAdmin)
            ->delete(route('admin.roles.destroy', $role))
            ->assertForbidden();

        $this->assertModelExists($role);
    }

    public function test_an_unassigned_role_can_be_deleted(): void
    {
        $superAdmin = $this->superAdmin();
        $role = Role::create(['name' => 'Unused Role', 'guard_name' => 'web']);

        $this->actingAs($superAdmin)
            ->delete(route('admin.roles.destroy', $role))
            ->assertRedirect(route('admin.roles.index'));

        $this->assertModelMissing($role);
    }

    public function test_only_a_super_admin_can_edit_the_super_admin_role(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $limitedRole = Role::create(['name' => 'Role Manager', 'guard_name' => 'web']);
        $limitedRole->givePermissionTo(['roles.view', 'roles.edit']);

        $limitedAdmin = User::factory()->create();
        $limitedAdmin->assignRole('Role Manager');

        $superAdminRole = Role::where('name', 'Super Admin')->firstOrFail();

        $this->actingAs($limitedAdmin)
            ->put(route('admin.roles.update', $superAdminRole), [
                'name' => 'Super Admin',
                'permissions' => ['pages.view'],
            ])
            ->assertForbidden();
    }
}
