<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesAdminUsers;
use Tests\TestCase;

class PermissionTest extends TestCase
{
    use CreatesAdminUsers, RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('admin.permissions.index'))->assertRedirect(route('login'));
    }

    public function test_super_admin_can_view_the_permission_list(): void
    {
        $this->actingAs($this->superAdmin())
            ->get(route('admin.permissions.index'))
            ->assertOk()
            ->assertSee('pages.view');
    }

    public function test_admin_can_view_the_permission_list(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.permissions.index'))
            ->assertOk()
            ->assertSee('pages.view');
    }

    /**
     * Editor/Viewer deliberately have no "permissions" module grant (see
     * config/permission_modules.php) - this is account/security-sensitive
     * data, not day-to-day content management.
     */
    public function test_editor_cannot_view_the_permission_list(): void
    {
        $this->actingAs($this->editor())
            ->get(route('admin.permissions.index'))
            ->assertForbidden();
    }

    public function test_viewer_cannot_view_the_permission_list(): void
    {
        $this->actingAs($this->viewer())
            ->get(route('admin.permissions.index'))
            ->assertForbidden();
    }

    public function test_a_user_without_permissions_cannot_view_the_permission_list(): void
    {
        $this->actingAs($this->userWithoutPermissions())
            ->get(route('admin.permissions.index'))
            ->assertForbidden();
    }
}
