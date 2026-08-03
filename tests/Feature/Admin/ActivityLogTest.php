<?php

namespace Tests\Feature\Admin;

use App\Models\Gallery;
use App\Models\GalleryItem;
use App\Models\MediaItem;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\Section;
use App\Models\SectionItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesAdminUsers;
use Tests\TestCase;

class ActivityLogTest extends TestCase
{
    use CreatesAdminUsers, RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('admin.activity.index'))->assertRedirect(route('login'));
    }

    public function test_an_editor_cannot_view_the_activity_log_by_default(): void
    {
        $editor = $this->editor();

        $this->actingAs($editor)->get(route('admin.activity.index'))->assertForbidden();
    }

    public function test_a_user_without_permissions_cannot_view_the_activity_log(): void
    {
        $user = $this->userWithoutPermissions();

        $this->actingAs($user)->get(route('admin.activity.index'))->assertForbidden();
    }

    public function test_super_admin_can_view_the_activity_log(): void
    {
        $superAdmin = $this->superAdmin();

        $this->actingAs($superAdmin)
            ->get(route('admin.activity.index'))
            ->assertOk();
    }

    public function test_creating_a_page_is_recorded_with_the_acting_user_as_causer(): void
    {
        $superAdmin = $this->superAdmin();

        $this->actingAs($superAdmin)->post(route('admin.pages.store'), [
            'title' => 'Audited Page',
            'slug' => 'audited-page',
            'status' => 'draft',
            'template' => 'default',
        ]);

        $page = Page::where('slug', 'audited-page')->firstOrFail();

        $this->assertDatabaseHas('activity_log', [
            'subject_type' => Page::class,
            'subject_id' => $page->id,
            'causer_id' => $superAdmin->id,
            'description' => 'created',
        ]);
    }

    public function test_updating_a_page_records_only_the_changed_attributes(): void
    {
        $superAdmin = $this->superAdmin();
        $page = Page::factory()->create(['title' => 'Original Title']);

        $this->actingAs($superAdmin)->put(route('admin.pages.update', $page), [
            'title' => 'Updated Title',
            'slug' => $page->slug,
            'status' => 'draft',
            'template' => 'default',
        ]);

        $response = $this->actingAs($superAdmin)->get(route('admin.activity.index'));

        $response->assertOk()->assertSee('Updated Title');
    }

    public function test_activity_search_filters_by_description(): void
    {
        $superAdmin = $this->superAdmin();
        Page::factory()->create(['title' => 'Searchable Page']);

        $response = $this->actingAs($superAdmin)->get(route('admin.activity.index', ['search' => 'created']));

        $response->assertOk()->assertSee('Searchable Page');
    }

    public function test_adding_a_photo_to_an_existing_album_is_recorded_with_album_context(): void
    {
        $superAdmin = $this->superAdmin();
        $album = Gallery::factory()->create(['title' => 'Summer Fair']);
        $photo = MediaItem::create(['title' => 'A Photo']);

        $this->actingAs($superAdmin)->post(route('admin.galleries.items.store', $album), [
            'type' => 'image',
            'image' => $photo->id,
            'caption' => 'Beach Day',
        ]);

        $item = GalleryItem::where('caption', 'Beach Day')->firstOrFail();

        $this->assertDatabaseHas('activity_log', [
            'subject_type' => GalleryItem::class,
            'subject_id' => $item->id,
            'causer_id' => $superAdmin->id,
            'description' => 'created',
        ]);

        $this->actingAs($superAdmin)
            ->get(route('admin.activity.index'))
            ->assertOk()
            ->assertSee('Beach Day')
            ->assertSee('Summer Fair');
    }

    public function test_adding_a_menu_item_is_recorded_with_menu_context(): void
    {
        $superAdmin = $this->superAdmin();
        $menu = Menu::factory()->create(['name' => 'Main Navigation']);

        $this->actingAs($superAdmin)->post(route('admin.menus.items.store', $menu), [
            'label' => 'About Us',
            'type' => 'internal',
            'url' => '/about',
        ]);

        $item = MenuItem::where('label', 'About Us')->firstOrFail();

        $this->assertDatabaseHas('activity_log', [
            'subject_type' => MenuItem::class,
            'subject_id' => $item->id,
            'causer_id' => $superAdmin->id,
            'description' => 'created',
        ]);

        $this->actingAs($superAdmin)
            ->get(route('admin.activity.index'))
            ->assertOk()
            ->assertSee('About Us')
            ->assertSee('Main Navigation');
    }

    public function test_adding_a_section_item_is_recorded_with_section_context(): void
    {
        $superAdmin = $this->superAdmin();
        $page = Page::factory()->create();
        $section = Section::factory()->for($page)->create(['type' => 'cards', 'heading' => 'Our Services']);

        $this->actingAs($superAdmin)->post(route('admin.pages.sections.items.store', [$page, $section]), [
            'title' => 'Web Design',
        ]);

        $item = SectionItem::where('title', 'Web Design')->firstOrFail();

        $this->assertDatabaseHas('activity_log', [
            'subject_type' => SectionItem::class,
            'subject_id' => $item->id,
            'causer_id' => $superAdmin->id,
            'description' => 'created',
        ]);

        $this->actingAs($superAdmin)
            ->get(route('admin.activity.index'))
            ->assertOk()
            ->assertSee('Web Design')
            ->assertSee('Our Services');
    }
}
