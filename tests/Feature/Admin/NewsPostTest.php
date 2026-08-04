<?php

namespace Tests\Feature\Admin;

use App\Models\NewsCategory;
use App\Models\NewsPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesAdminUsers;
use Tests\TestCase;

class NewsPostTest extends TestCase
{
    use CreatesAdminUsers, RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $post = NewsPost::factory()->create();

        $this->get(route('admin.news.index'))->assertRedirect(route('login'));
        $this->get(route('admin.news.edit', $post))->assertRedirect(route('login'));
    }

    public function test_a_user_without_permissions_cannot_view_news(): void
    {
        $user = $this->userWithoutPermissions();

        $this->actingAs($user)->get(route('admin.news.index'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.news.create'))->assertForbidden();
    }

    public function test_editor_can_view_the_news_list(): void
    {
        $editor = $this->editor();
        NewsPost::factory()->create(['title' => 'New Scholarship Round']);

        $this->actingAs($editor)
            ->get(route('admin.news.index'))
            ->assertOk()
            ->assertSee('New Scholarship Round');
    }

    public function test_editor_can_create_a_post_with_a_category(): void
    {
        $editor = $this->editor();
        $category = NewsCategory::factory()->create();

        $response = $this->actingAs($editor)->post(route('admin.news.store'), [
            'category_id' => $category->id,
            'title' => 'New Office Opening',
            'slug' => 'new-office-opening',
            'status' => 'draft',
        ]);

        $response->assertRedirect(route('admin.news.index'));
        $this->assertDatabaseHas('news_posts', [
            'slug' => 'new-office-opening',
            'category_id' => $category->id,
        ]);
    }

    public function test_a_user_without_permissions_cannot_create_a_post(): void
    {
        $user = $this->userWithoutPermissions();

        $this->actingAs($user)->post(route('admin.news.store'), [
            'title' => 'Sneaky Post',
            'slug' => 'sneaky-post',
            'status' => 'draft',
        ])->assertForbidden();

        $this->assertDatabaseMissing('news_posts', ['slug' => 'sneaky-post']);
    }

    public function test_creating_a_post_requires_title_and_slug(): void
    {
        $editor = $this->editor();

        $this->actingAs($editor)
            ->post(route('admin.news.store'), ['slug' => 'no-title', 'status' => 'draft'])
            ->assertSessionHasErrors('title');
    }

    public function test_a_post_slug_must_be_unique(): void
    {
        $editor = $this->editor();
        NewsPost::factory()->create(['slug' => 'existing-post']);

        $this->actingAs($editor)
            ->post(route('admin.news.store'), [
                'title' => 'Duplicate',
                'slug' => 'existing-post',
                'status' => 'draft',
            ])
            ->assertSessionHasErrors('slug');
    }

    public function test_editor_can_update_a_post(): void
    {
        $editor = $this->editor();
        $post = NewsPost::factory()->create(['title' => 'Old Title', 'slug' => 'old-title']);

        $response = $this->actingAs($editor)->put(route('admin.news.update', $post), [
            'title' => 'New Title',
            'slug' => 'old-title',
            'status' => 'published',
            'is_featured' => true,
        ]);

        $response->assertRedirect(route('admin.news.index'));
        $post->refresh();
        $this->assertSame('New Title', $post->title);
        $this->assertSame('published', $post->status);
        $this->assertTrue($post->is_featured);
    }

    public function test_editor_can_delete_a_post(): void
    {
        $editor = $this->editor();
        $post = NewsPost::factory()->create();

        $this->actingAs($editor)
            ->delete(route('admin.news.destroy', $post))
            ->assertRedirect(route('admin.news.index'));

        $this->assertNull(NewsPost::find($post->id));
        $this->assertSoftDeleted($post);
    }

    public function test_editor_can_restore_a_trashed_post(): void
    {
        $editor = $this->editor();
        $post = NewsPost::factory()->create();
        $post->delete();

        $this->actingAs($editor)
            ->post(route('admin.news.restore', $post))
            ->assertRedirect(route('admin.news.trash'));

        $this->assertNotSoftDeleted($post);
    }
}
