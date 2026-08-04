<?php

namespace Tests\Feature\Admin;

use App\Models\NewsCategory;
use App\Models\NewsPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesAdminUsers;
use Tests\TestCase;

class NewsCategoryTest extends TestCase
{
    use CreatesAdminUsers, RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('admin.news-categories.index'))->assertRedirect(route('login'));
    }

    public function test_a_user_without_permissions_cannot_manage_categories(): void
    {
        $user = $this->userWithoutPermissions();

        $this->actingAs($user)->get(route('admin.news-categories.index'))->assertForbidden();
    }

    public function test_editor_can_create_a_category(): void
    {
        $editor = $this->editor();

        $response = $this->actingAs($editor)->post(route('admin.news-categories.store'), [
            'name' => 'Events',
            'slug' => 'events',
        ]);

        $response->assertRedirect(route('admin.news-categories.index'));
        $this->assertDatabaseHas('news_categories', ['slug' => 'events']);
    }

    public function test_a_category_slug_must_be_unique(): void
    {
        $editor = $this->editor();
        NewsCategory::factory()->create(['slug' => 'press']);

        $this->actingAs($editor)
            ->post(route('admin.news-categories.store'), ['name' => 'Press', 'slug' => 'press'])
            ->assertSessionHasErrors('slug');
    }

    public function test_editor_can_update_a_category(): void
    {
        $editor = $this->editor();
        $category = NewsCategory::factory()->create(['name' => 'Old Name']);

        $this->actingAs($editor)->put(route('admin.news-categories.update', $category), [
            'name' => 'New Name',
            'slug' => $category->slug,
        ])->assertRedirect(route('admin.news-categories.index'));

        $this->assertSame('New Name', $category->fresh()->name);
    }

    public function test_editor_can_delete_an_empty_category(): void
    {
        $editor = $this->editor();
        $category = NewsCategory::factory()->create();

        $this->actingAs($editor)
            ->delete(route('admin.news-categories.destroy', $category))
            ->assertRedirect(route('admin.news-categories.index'));

        $this->assertModelMissing($category);
    }

    public function test_deleting_a_category_does_not_delete_its_posts(): void
    {
        $editor = $this->editor();
        $category = NewsCategory::factory()->create();
        $post = NewsPost::factory()->create(['category_id' => $category->id]);

        $this->actingAs($editor)->delete(route('admin.news-categories.destroy', $category));

        $this->assertModelExists($post->fresh());
        $this->assertNull($post->fresh()->category_id);
    }
}
