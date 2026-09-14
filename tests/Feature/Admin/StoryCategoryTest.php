<?php

namespace Tests\Feature\Admin;

use App\Models\Story;
use App\Models\StoryCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesAdminUsers;
use Tests\TestCase;

class StoryCategoryTest extends TestCase
{
    use CreatesAdminUsers, RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('admin.story-categories.index'))->assertRedirect(route('login'));
    }

    public function test_a_user_without_permissions_cannot_manage_categories(): void
    {
        $user = $this->userWithoutPermissions();

        $this->actingAs($user)->get(route('admin.story-categories.index'))->assertForbidden();
    }

    public function test_editor_can_create_a_category(): void
    {
        $editor = $this->editor();

        $response = $this->actingAs($editor)->post(route('admin.story-categories.store'), [
            'name' => 'Education',
            'slug' => 'education',
        ]);

        $response->assertRedirect(route('admin.story-categories.index'));
        $this->assertDatabaseHas('story_categories', ['slug' => 'education']);
    }

    public function test_a_category_slug_must_be_unique(): void
    {
        $editor = $this->editor();
        StoryCategory::factory()->create(['slug' => 'health']);

        $this->actingAs($editor)
            ->post(route('admin.story-categories.store'), ['name' => 'Health', 'slug' => 'health'])
            ->assertSessionHasErrors('slug');
    }

    public function test_editor_can_update_a_category(): void
    {
        $editor = $this->editor();
        $category = StoryCategory::factory()->create(['name' => 'Old Name']);

        $this->actingAs($editor)->put(route('admin.story-categories.update', $category), [
            'name' => 'New Name',
            'slug' => $category->slug,
        ])->assertRedirect(route('admin.story-categories.index'));

        $this->assertSame('New Name', $category->fresh()->name);
    }

    public function test_editor_can_delete_an_empty_category(): void
    {
        $editor = $this->editor();
        $category = StoryCategory::factory()->create();

        $this->actingAs($editor)
            ->delete(route('admin.story-categories.destroy', $category))
            ->assertRedirect(route('admin.story-categories.index'));

        $this->assertModelMissing($category);
    }

    public function test_deleting_a_category_does_not_delete_its_stories(): void
    {
        $editor = $this->editor();
        $category = StoryCategory::factory()->create();
        $story = Story::factory()->create(['category_id' => $category->id]);

        $this->actingAs($editor)->delete(route('admin.story-categories.destroy', $category));

        $this->assertModelExists($story->fresh());
        $this->assertNull($story->fresh()->category_id);
    }
}
