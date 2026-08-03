<?php

namespace Tests\Feature\Admin;

use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesAdminUsers;
use Tests\TestCase;

class PageTrashTest extends TestCase
{
    use CreatesAdminUsers, RefreshDatabase;

    public function test_deleting_a_page_soft_deletes_it(): void
    {
        $editor = $this->editor();
        $page = Page::factory()->create();

        $this->actingAs($editor)->delete(route('admin.pages.destroy', $page));

        $this->assertNull(Page::find($page->id));
        $trashed = Page::withTrashed()->find($page->id);
        $this->assertNotNull($trashed);
        $this->assertNotNull($trashed->deleted_at);
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('admin.pages.trash'))->assertRedirect(route('login'));
    }

    public function test_a_user_without_permissions_cannot_view_trash(): void
    {
        $user = $this->userWithoutPermissions();

        $this->actingAs($user)->get(route('admin.pages.trash'))->assertForbidden();
    }

    public function test_the_trash_lists_only_deleted_pages(): void
    {
        $editor = $this->editor();
        $active = Page::factory()->create(['title' => 'Still Here']);
        $trashed = Page::factory()->create(['title' => 'Gone']);
        $trashed->delete();

        $response = $this->actingAs($editor)->get(route('admin.pages.trash'));

        $response->assertOk()->assertSee('Gone')->assertDontSee('Still Here');
    }

    public function test_editor_can_restore_a_trashed_page(): void
    {
        $editor = $this->editor();
        $page = Page::factory()->create();
        $page->delete();

        $this->actingAs($editor)
            ->post(route('admin.pages.restore', $page))
            ->assertRedirect(route('admin.pages.trash'));

        $this->assertNotNull(Page::find($page->id));
    }

    public function test_a_user_without_permissions_cannot_restore_a_page(): void
    {
        $user = $this->userWithoutPermissions();
        $page = Page::factory()->create();
        $page->delete();

        $this->actingAs($user)
            ->post(route('admin.pages.restore', $page))
            ->assertForbidden();

        $this->assertNull(Page::find($page->id));
    }

    public function test_editor_can_permanently_delete_a_trashed_page(): void
    {
        $editor = $this->editor();
        $page = Page::factory()->create();
        $page->delete();

        $this->actingAs($editor)
            ->delete(route('admin.pages.force-delete', $page))
            ->assertRedirect(route('admin.pages.trash'));

        $this->assertNull(Page::withTrashed()->find($page->id));
    }

    public function test_a_user_without_permissions_cannot_permanently_delete_a_page(): void
    {
        $user = $this->userWithoutPermissions();
        $page = Page::factory()->create();
        $page->delete();

        $this->actingAs($user)
            ->delete(route('admin.pages.force-delete', $page))
            ->assertForbidden();

        $this->assertNotNull(Page::withTrashed()->find($page->id));
    }

    public function test_a_trashed_pages_slug_still_blocks_reuse_until_purged(): void
    {
        $editor = $this->editor();
        $page = Page::factory()->create(['slug' => 'about-us']);
        $page->delete();

        $this->actingAs($editor)
            ->post(route('admin.pages.store'), [
                'title' => 'New About Page',
                'slug' => 'about-us',
                'status' => 'draft',
                'template' => 'default',
            ])
            ->assertSessionHasErrors('slug');
    }
}
