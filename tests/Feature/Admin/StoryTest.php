<?php

namespace Tests\Feature\Admin;

use App\Models\Project;
use App\Models\Story;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesAdminUsers;
use Tests\TestCase;

class StoryTest extends TestCase
{
    use CreatesAdminUsers, RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $story = Story::factory()->create();

        $this->get(route('admin.stories.index'))->assertRedirect(route('login'));
        $this->get(route('admin.stories.edit', $story))->assertRedirect(route('login'));
    }

    public function test_a_user_without_permissions_cannot_view_stories(): void
    {
        $user = $this->userWithoutPermissions();

        $this->actingAs($user)->get(route('admin.stories.index'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.stories.create'))->assertForbidden();
    }

    public function test_editor_can_view_the_story_list(): void
    {
        $editor = $this->editor();
        Story::factory()->create(['title' => 'From Laborer to Technician']);

        $this->actingAs($editor)
            ->get(route('admin.stories.index'))
            ->assertOk()
            ->assertSee('From Laborer to Technician');
    }

    public function test_editor_can_create_a_story_with_a_project(): void
    {
        $editor = $this->editor();
        $project = Project::factory()->create();

        $response = $this->actingAs($editor)->post(route('admin.stories.store'), [
            'project_id' => $project->id,
            'title' => 'A New Success Story',
            'slug' => 'a-new-success-story',
            'status' => 'draft',
        ]);

        $response->assertRedirect(route('admin.stories.index'));
        $this->assertDatabaseHas('stories', [
            'slug' => 'a-new-success-story',
            'project_id' => $project->id,
        ]);
    }

    public function test_a_user_without_permissions_cannot_create_a_story(): void
    {
        $user = $this->userWithoutPermissions();

        $this->actingAs($user)->post(route('admin.stories.store'), [
            'title' => 'Sneaky Story',
            'slug' => 'sneaky-story',
            'status' => 'draft',
        ])->assertForbidden();

        $this->assertDatabaseMissing('stories', ['slug' => 'sneaky-story']);
    }

    public function test_creating_a_story_requires_title_and_slug(): void
    {
        $editor = $this->editor();

        $this->actingAs($editor)
            ->post(route('admin.stories.store'), ['slug' => 'no-title', 'status' => 'draft'])
            ->assertSessionHasErrors('title');
    }

    public function test_a_story_slug_must_be_unique(): void
    {
        $editor = $this->editor();
        Story::factory()->create(['slug' => 'existing-story']);

        $this->actingAs($editor)
            ->post(route('admin.stories.store'), [
                'title' => 'Duplicate',
                'slug' => 'existing-story',
                'status' => 'draft',
            ])
            ->assertSessionHasErrors('slug');
    }

    public function test_editor_can_update_a_story(): void
    {
        $editor = $this->editor();
        $story = Story::factory()->create(['title' => 'Old Title', 'slug' => 'old-title']);

        $response = $this->actingAs($editor)->put(route('admin.stories.update', $story), [
            'title' => 'New Title',
            'slug' => 'old-title',
            'status' => 'published',
            'is_featured' => true,
        ]);

        $response->assertRedirect(route('admin.stories.index'));
        $story->refresh();
        $this->assertSame('New Title', $story->title);
        $this->assertSame('published', $story->status);
        $this->assertTrue($story->is_featured);
    }

    public function test_editor_can_delete_a_story(): void
    {
        $editor = $this->editor();
        $story = Story::factory()->create();

        $this->actingAs($editor)
            ->delete(route('admin.stories.destroy', $story))
            ->assertRedirect(route('admin.stories.index'));

        $this->assertNull(Story::find($story->id));
        $this->assertSoftDeleted($story);
    }

    public function test_editor_can_restore_a_trashed_story(): void
    {
        $editor = $this->editor();
        $story = Story::factory()->create();
        $story->delete();

        $this->actingAs($editor)
            ->post(route('admin.stories.restore', $story))
            ->assertRedirect(route('admin.stories.trash'));

        $this->assertNotSoftDeleted($story);
    }
}
