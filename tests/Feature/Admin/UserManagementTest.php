<?php

namespace Tests\Feature\Admin;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesAdminUsers;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use CreatesAdminUsers, RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('admin.users.index'))->assertRedirect(route('login'));
    }

    public function test_editor_cannot_manage_users(): void
    {
        $editor = $this->editor();

        $this->actingAs($editor)->get(route('admin.users.index'))->assertForbidden();
        $this->actingAs($editor)->get(route('admin.users.create'))->assertForbidden();
    }

    public function test_super_admin_can_create_a_user_with_a_role(): void
    {
        $superAdmin = $this->superAdmin();

        $response = $this->actingAs($superAdmin)->post(route('admin.users.store'), [
            'name' => 'New Editor',
            'email' => 'new-editor@example.test',
            'password' => 'Password1234',
            'password_confirmation' => 'Password1234',
            'role' => 'Editor',
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $user = User::where('email', 'new-editor@example.test')->firstOrFail();
        $this->assertTrue($user->hasRole('Editor'));
    }

    public function test_super_admin_can_update_a_user(): void
    {
        $superAdmin = $this->superAdmin();
        $target = User::factory()->create();
        $target->assignRole('Editor');

        $response = $this->actingAs($superAdmin)->put(route('admin.users.update', $target), [
            'name' => 'Renamed User',
            'email' => $target->email,
            'role' => 'Editor',
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertSame('Renamed User', $target->fresh()->name);
    }

    public function test_super_admin_can_deactivate_a_user_via_the_edit_form(): void
    {
        // Regression test: the "Active" toggle used to be a bare checkbox
        // with no hidden fallback input, so unchecking it submitted nothing
        // at all and UserService::update() silently kept the old value.
        $superAdmin = $this->superAdmin();
        $target = User::factory()->create(['is_active' => true]);
        $target->assignRole('Editor');

        $response = $this->actingAs($superAdmin)->put(route('admin.users.update', $target), [
            'name' => $target->name,
            'email' => $target->email,
            'role' => 'Editor',
            'is_active' => '0',
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertFalse($target->fresh()->is_active);
    }

    public function test_super_admin_can_delete_another_user(): void
    {
        $superAdmin = $this->superAdmin();
        $target = User::factory()->create();
        $target->assignRole('Editor');

        $this->actingAs($superAdmin)
            ->delete(route('admin.users.destroy', $target))
            ->assertRedirect(route('admin.users.index'));

        $this->assertModelMissing($target);
    }

    public function test_a_user_cannot_delete_themselves(): void
    {
        $superAdmin = $this->superAdmin();

        $this->actingAs($superAdmin)
            ->delete(route('admin.users.destroy', $superAdmin))
            ->assertForbidden();

        $this->assertModelExists($superAdmin);
    }

    public function test_only_a_super_admin_can_assign_the_super_admin_role(): void
    {
        // A role that can create users but is not itself "Super Admin" -
        // simulates a lower-privileged admin trying to self-escalate.
        $this->seed(RolesAndPermissionsSeeder::class);
        $limitedRole = Role::create(['name' => 'User Manager', 'guard_name' => 'web']);
        $limitedRole->givePermissionTo(['users.view', 'users.create', 'users.edit']);

        $limitedAdmin = User::factory()->create();
        $limitedAdmin->assignRole('User Manager');

        $response = $this->actingAs($limitedAdmin)->post(route('admin.users.store'), [
            'name' => 'Sneaky',
            'email' => 'sneaky@example.test',
            'password' => 'Password1234',
            'password_confirmation' => 'Password1234',
            'role' => 'Super Admin',
        ]);

        $response->assertSessionHasErrors('role');
        $this->assertDatabaseMissing('users', ['email' => 'sneaky@example.test']);
    }

    public function test_super_admin_can_assign_the_super_admin_role(): void
    {
        $superAdmin = $this->superAdmin();

        $response = $this->actingAs($superAdmin)->post(route('admin.users.store'), [
            'name' => 'Another Super Admin',
            'email' => 'another-super@example.test',
            'password' => 'Password1234',
            'password_confirmation' => 'Password1234',
            'role' => 'Super Admin',
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $user = User::where('email', 'another-super@example.test')->firstOrFail();
        $this->assertTrue($user->hasRole('Super Admin'));
    }
}
