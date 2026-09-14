<?php

namespace Tests\Feature\Admin;

use App\Models\Project;
use App\Models\Story;
use App\Models\StoryCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\CreatesAdminUsers;
use Tests\TestCase;

class StoryTest extends TestCase
{
    use CreatesAdminUsers, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
    }

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

    public function test_editor_can_create_a_story_with_a_category(): void
    {
        $editor = $this->editor();
        $category = StoryCategory::factory()->create();

        $response = $this->actingAs($editor)->post(route('admin.stories.store'), [
            'category_id' => $category->id,
            'title' => 'A Categorized Story',
            'slug' => 'a-categorized-story',
            'status' => 'draft',
        ]);

        $response->assertRedirect(route('admin.stories.index'));
        $this->assertDatabaseHas('stories', [
            'slug' => 'a-categorized-story',
            'category_id' => $category->id,
        ]);
    }

    public function test_editor_can_upload_an_attachment_with_a_story(): void
    {
        $editor = $this->editor();
        $file = UploadedFile::fake()->create('report.pdf', 100);

        $this->actingAs($editor)->post(route('admin.stories.store'), [
            'title' => 'Story With Attachment',
            'slug' => 'story-with-attachment',
            'status' => 'published',
            'attachment' => $file,
        ])->assertRedirect(route('admin.stories.index'));

        $story = Story::where('slug', 'story-with-attachment')->firstOrFail();
        $this->assertNotNull($story->attachment_url);
        $this->assertSame('report.pdf', $story->attachment_file_name);
    }

    public function test_an_attachment_must_be_an_allowed_file_type(): void
    {
        $editor = $this->editor();
        $file = UploadedFile::fake()->create('malware.exe', 100);

        $this->actingAs($editor)
            ->post(route('admin.stories.store'), [
                'title' => 'Bad Attachment',
                'slug' => 'bad-attachment',
                'status' => 'draft',
                'attachment' => $file,
            ])
            ->assertSessionHasErrors('attachment');
    }

    public function test_editor_can_remove_an_existing_attachment(): void
    {
        $editor = $this->editor();
        $story = Story::factory()->create(['title' => 'Has Attachment', 'slug' => 'has-attachment']);
        $story->addMedia(UploadedFile::fake()->create('old.pdf', 50))->toMediaCollection('attachment');
        $this->assertNotNull($story->fresh()->attachment_url);

        $this->actingAs($editor)->put(route('admin.stories.update', $story), [
            'title' => 'Has Attachment',
            'slug' => 'has-attachment',
            'status' => 'draft',
            'remove_attachment' => true,
        ])->assertRedirect(route('admin.stories.index'));

        $this->assertNull($story->fresh()->attachment_url);
    }
}
