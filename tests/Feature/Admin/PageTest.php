<?php

namespace Tests\Feature\Admin;

use App\Models\MediaItem;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesAdminUsers;
use Tests\TestCase;

class PageTest extends TestCase
{
    use CreatesAdminUsers, RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $page = Page::factory()->create();

        $this->get(route('admin.pages.index'))->assertRedirect(route('login'));
        $this->get(route('admin.pages.create'))->assertRedirect(route('login'));
        $this->get(route('admin.pages.edit', $page))->assertRedirect(route('login'));
    }

    public function test_a_user_without_permissions_cannot_view_pages(): void
    {
        $user = $this->userWithoutPermissions();

        $this->actingAs($user)->get(route('admin.pages.index'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.pages.create'))->assertForbidden();
    }

    public function test_editor_can_view_the_page_list(): void
    {
        $editor = $this->editor();
        Page::factory()->create(['title' => 'About Us']);

        $this->actingAs($editor)
            ->get(route('admin.pages.index'))
            ->assertOk()
            ->assertSee('About Us');
    }

    public function test_editor_can_create_a_page(): void
    {
        $editor = $this->editor();

        $response = $this->actingAs($editor)->post(route('admin.pages.store'), [
            'title' => 'Contact Us',
            'slug' => 'contact-us',
            'status' => 'draft',
            'template' => 'default',
        ]);

        $response->assertRedirect(route('admin.pages.index'));
        $this->assertDatabaseHas('pages', ['slug' => 'contact-us', 'title' => 'Contact Us']);
    }

    public function test_editor_can_set_a_page_specific_banner_override(): void
    {
        $editor = $this->editor();
        $photo = MediaItem::create(['title' => 'Campus']);

        $this->actingAs($editor)->post(route('admin.pages.store'), [
            'title' => 'About',
            'slug' => 'about',
            'status' => 'published',
            'template' => 'default',
            'banner_image' => $photo->id,
            'banner_eyebrow' => 'About RSUF',
        ])->assertRedirect(route('admin.pages.index'));

        $this->assertDatabaseHas('pages', [
            'slug' => 'about',
            'banner_image' => $photo->id,
            'banner_eyebrow' => 'About RSUF',
        ]);
    }

    public function test_a_user_without_permissions_cannot_create_a_page(): void
    {
        $user = $this->userWithoutPermissions();

        $response = $this->actingAs($user)->post(route('admin.pages.store'), [
            'title' => 'Contact Us',
            'slug' => 'contact-us',
            'status' => 'draft',
            'template' => 'default',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('pages', ['slug' => 'contact-us']);
    }

    public function test_creating_a_page_requires_a_title_and_slug(): void
    {
        $editor = $this->editor();

        $this->actingAs($editor)
            ->post(route('admin.pages.store'), ['slug' => 'no-title', 'status' => 'draft', 'template' => 'default'])
            ->assertSessionHasErrors('title');

        $this->actingAs($editor)
            ->post(route('admin.pages.store'), ['title' => 'No Slug', 'status' => 'draft', 'template' => 'default'])
            ->assertSessionHasErrors('slug');
    }

    public function test_a_page_cannot_use_a_reserved_slug(): void
    {
        $editor = $this->editor();

        $this->actingAs($editor)
            ->post(route('admin.pages.store'), [
                'title' => 'Admin Page',
                'slug' => 'admin',
                'status' => 'draft',
                'template' => 'default',
            ])
            ->assertSessionHasErrors('slug');
    }

    public function test_a_page_slug_must_be_unique(): void
    {
        $editor = $this->editor();
        Page::factory()->create(['slug' => 'about']);

        $this->actingAs($editor)
            ->post(route('admin.pages.store'), [
                'title' => 'Duplicate',
                'slug' => 'about',
                'status' => 'draft',
                'template' => 'default',
            ])
            ->assertSessionHasErrors('slug');
    }

    public function test_editor_can_update_a_page(): void
    {
        $editor = $this->editor();
        $page = Page::factory()->create(['title' => 'Old Title', 'slug' => 'old-title']);

        $response = $this->actingAs($editor)->put(route('admin.pages.update', $page), [
            'title' => 'New Title',
            'slug' => 'old-title',
            'status' => 'published',
            'template' => 'default',
        ]);

        $response->assertRedirect(route('admin.pages.index'));
        $this->assertSame('New Title', $page->fresh()->title);
        $this->assertSame('published', $page->fresh()->status);
    }

    public function test_editor_can_delete_a_page(): void
    {
        $editor = $this->editor();
        $page = Page::factory()->create();

        $this->actingAs($editor)
            ->delete(route('admin.pages.destroy', $page))
            ->assertRedirect(route('admin.pages.index'));

        // Deleting soft-deletes (see PageTrashTest for the trash/restore/force-delete flow).
        $this->assertNull(Page::find($page->id));
        $this->assertSoftDeleted($page);
    }

    public function test_a_user_without_permissions_cannot_delete_a_page(): void
    {
        $user = $this->userWithoutPermissions();
        $page = Page::factory()->create();

        $this->actingAs($user)
            ->delete(route('admin.pages.destroy', $page))
            ->assertForbidden();

        $this->assertModelExists($page);
    }

    public function test_super_admin_has_full_access(): void
    {
        $superAdmin = $this->superAdmin();

        $this->actingAs($superAdmin)->get(route('admin.pages.index'))->assertOk();

        $this->actingAs($superAdmin)->post(route('admin.pages.store'), [
            'title' => 'Super Admin Page',
            'slug' => 'super-admin-page',
            'status' => 'draft',
            'template' => 'default',
        ])->assertRedirect(route('admin.pages.index'));

        $this->assertDatabaseHas('pages', ['slug' => 'super-admin-page']);
    }
}
