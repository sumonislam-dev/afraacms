<?php

namespace Tests\Feature\Admin;

use App\Models\Menu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesAdminUsers;
use Tests\TestCase;

class MenuTest extends TestCase
{
    use CreatesAdminUsers, RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('admin.menus.index'))->assertRedirect(route('login'));
    }

    public function test_a_user_without_permissions_cannot_view_menus(): void
    {
        $user = $this->userWithoutPermissions();

        $this->actingAs($user)->get(route('admin.menus.index'))->assertForbidden();
    }

    public function test_editor_can_view_the_menu_list(): void
    {
        $editor = $this->editor();
        Menu::factory()->create(['name' => 'Main Navigation']);

        $this->actingAs($editor)
            ->get(route('admin.menus.index'))
            ->assertOk()
            ->assertSee('Main Navigation');
    }

    public function test_editor_can_create_a_menu(): void
    {
        $editor = $this->editor();

        $response = $this->actingAs($editor)->post(route('admin.menus.store'), [
            'name' => 'Footer Menu',
            'slug' => 'footer-menu',
        ]);

        $menu = Menu::where('slug', 'footer-menu')->firstOrFail();
        $response->assertRedirect(route('admin.menus.edit', $menu));
    }

    public function test_a_menu_slug_must_be_unique(): void
    {
        $editor = $this->editor();
        Menu::factory()->create(['slug' => 'existing-menu']);

        $this->actingAs($editor)
            ->post(route('admin.menus.store'), ['name' => 'Duplicate', 'slug' => 'existing-menu'])
            ->assertSessionHasErrors('slug');
    }

    public function test_a_user_without_permissions_cannot_create_a_menu(): void
    {
        $user = $this->userWithoutPermissions();

        $this->actingAs($user)->post(route('admin.menus.store'), [
            'name' => 'Sneaky Menu',
            'slug' => 'sneaky-menu',
        ])->assertForbidden();

        $this->assertDatabaseMissing('menus', ['slug' => 'sneaky-menu']);
    }

    public function test_editor_can_rename_a_menu(): void
    {
        $editor = $this->editor();
        $menu = Menu::factory()->create(['name' => 'Old Name']);

        $this->actingAs($editor)->put(route('admin.menus.update', $menu), [
            'name' => 'New Name',
            'slug' => $menu->slug,
        ])->assertRedirect(route('admin.menus.edit', $menu));

        $this->assertSame('New Name', $menu->fresh()->name);
    }

    public function test_editor_can_delete_a_menu(): void
    {
        $editor = $this->editor();
        $menu = Menu::factory()->create();

        $this->actingAs($editor)
            ->delete(route('admin.menus.destroy', $menu))
            ->assertRedirect(route('admin.menus.index'));

        $this->assertModelMissing($menu);
    }

    public function test_a_user_without_permissions_cannot_delete_a_menu(): void
    {
        $user = $this->userWithoutPermissions();
        $menu = Menu::factory()->create();

        $this->actingAs($user)
            ->delete(route('admin.menus.destroy', $menu))
            ->assertForbidden();

        $this->assertModelExists($menu);
    }
}
