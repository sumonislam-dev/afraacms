<?php

namespace Tests\Feature\Admin;

use App\Models\Menu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesAdminUsers;
use Tests\TestCase;

class MenuItemTest extends TestCase
{
    use CreatesAdminUsers, RefreshDatabase;

    public function test_editor_can_add_an_item_to_a_menu(): void
    {
        $editor = $this->editor();
        $menu = Menu::factory()->create();

        $response = $this->actingAs($editor)->post(route('admin.menus.items.store', $menu), [
            'label' => 'About',
            'type' => 'internal',
            'url' => '/about',
        ]);

        $response->assertRedirect(route('admin.menus.edit', $menu));
        $this->assertDatabaseHas('menu_items', ['menu_id' => $menu->id, 'label' => 'About', 'url' => '/about']);
    }

    public function test_a_menu_item_requires_a_label_and_url(): void
    {
        $editor = $this->editor();
        $menu = Menu::factory()->create();

        $this->actingAs($editor)
            ->post(route('admin.menus.items.store', $menu), ['type' => 'internal'])
            ->assertSessionHasErrors(['label', 'url']);
    }

    public function test_a_menu_item_type_must_be_internal_or_external(): void
    {
        $editor = $this->editor();
        $menu = Menu::factory()->create();

        $this->actingAs($editor)
            ->post(route('admin.menus.items.store', $menu), [
                'label' => 'Bad Type',
                'type' => 'not-a-real-type',
                'url' => '/x',
            ])
            ->assertSessionHasErrors('type');
    }

    public function test_a_user_without_permissions_cannot_add_menu_items(): void
    {
        $user = $this->userWithoutPermissions();
        $menu = Menu::factory()->create();

        $this->actingAs($user)->post(route('admin.menus.items.store', $menu), [
            'label' => 'About',
            'type' => 'internal',
            'url' => '/about',
        ])->assertForbidden();

        $this->assertDatabaseCount('menu_items', 0);
    }

    public function test_editor_can_update_a_menu_item(): void
    {
        $editor = $this->editor();
        $menu = Menu::factory()->create();
        $item = $menu->items()->create(['label' => 'Old Label', 'type' => 'internal', 'url' => '/old', 'sort_order' => 0]);

        $this->actingAs($editor)->put(route('admin.menus.items.update', [$menu, $item]), [
            'label' => 'New Label',
            'type' => 'internal',
            'url' => '/new',
            'open_in_new_tab' => true,
        ])->assertRedirect(route('admin.menus.edit', $menu));

        $item->refresh();
        $this->assertSame('New Label', $item->label);
        $this->assertTrue($item->open_in_new_tab);
    }

    public function test_editor_can_delete_a_menu_item(): void
    {
        $editor = $this->editor();
        $menu = Menu::factory()->create();
        $item = $menu->items()->create(['label' => 'Item', 'type' => 'internal', 'url' => '/x', 'sort_order' => 0]);

        $this->actingAs($editor)
            ->delete(route('admin.menus.items.destroy', [$menu, $item]))
            ->assertRedirect(route('admin.menus.edit', $menu));

        $this->assertModelMissing($item);
    }

    public function test_a_menu_item_from_a_different_menu_cannot_be_deleted_through_this_menu(): void
    {
        $editor = $this->editor();
        $menuA = Menu::factory()->create();
        $menuB = Menu::factory()->create();
        $item = $menuB->items()->create(['label' => 'Item', 'type' => 'internal', 'url' => '/x', 'sort_order' => 0]);

        $this->actingAs($editor)
            ->delete(route('admin.menus.items.destroy', [$menuA, $item]))
            ->assertNotFound();

        $this->assertModelExists($item);
    }
}
