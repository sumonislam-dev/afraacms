<?php

namespace Tests\Feature\Admin;

use App\Models\Project;
use App\Models\ProjectCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesAdminUsers;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use CreatesAdminUsers, RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $project = Project::factory()->create();

        $this->get(route('admin.projects.index'))->assertRedirect(route('login'));
        $this->get(route('admin.projects.edit', $project))->assertRedirect(route('login'));
    }

    public function test_a_user_without_permissions_cannot_view_projects(): void
    {
        $user = $this->userWithoutPermissions();

        $this->actingAs($user)->get(route('admin.projects.index'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.projects.create'))->assertForbidden();
    }

    public function test_editor_can_view_the_project_list(): void
    {
        $editor = $this->editor();
        Project::factory()->create(['title' => 'Riverside Tower']);

        $this->actingAs($editor)
            ->get(route('admin.projects.index'))
            ->assertOk()
            ->assertSee('Riverside Tower');
    }

    public function test_editor_can_create_a_project_with_a_category(): void
    {
        $editor = $this->editor();
        $category = ProjectCategory::factory()->create();

        $response = $this->actingAs($editor)->post(route('admin.projects.store'), [
            'category_id' => $category->id,
            'title' => 'New Office Tower',
            'slug' => 'new-office-tower',
            'status' => 'draft',
        ]);

        $response->assertRedirect(route('admin.projects.index'));
        $this->assertDatabaseHas('projects', [
            'slug' => 'new-office-tower',
            'category_id' => $category->id,
        ]);
    }

    public function test_a_user_without_permissions_cannot_create_a_project(): void
    {
        $user = $this->userWithoutPermissions();

        $this->actingAs($user)->post(route('admin.projects.store'), [
            'title' => 'Sneaky Project',
            'slug' => 'sneaky-project',
            'status' => 'draft',
        ])->assertForbidden();

        $this->assertDatabaseMissing('projects', ['slug' => 'sneaky-project']);
    }

    public function test_creating_a_project_requires_title_and_slug(): void
    {
        $editor = $this->editor();

        $this->actingAs($editor)
            ->post(route('admin.projects.store'), ['slug' => 'no-title', 'status' => 'draft'])
            ->assertSessionHasErrors('title');
    }

    public function test_a_project_slug_must_be_unique(): void
    {
        $editor = $this->editor();
        Project::factory()->create(['slug' => 'existing-project']);

        $this->actingAs($editor)
            ->post(route('admin.projects.store'), [
                'title' => 'Duplicate',
                'slug' => 'existing-project',
                'status' => 'draft',
            ])
            ->assertSessionHasErrors('slug');
    }

    public function test_a_project_category_must_exist(): void
    {
        $editor = $this->editor();

        $this->actingAs($editor)
            ->post(route('admin.projects.store'), [
                'category_id' => 999999,
                'title' => 'Orphan Project',
                'slug' => 'orphan-project',
                'status' => 'draft',
            ])
            ->assertSessionHasErrors('category_id');
    }

    public function test_editor_can_update_a_project(): void
    {
        $editor = $this->editor();
        $project = Project::factory()->create(['title' => 'Old Name', 'slug' => 'old-name']);

        $response = $this->actingAs($editor)->put(route('admin.projects.update', $project), [
            'title' => 'New Name',
            'slug' => 'old-name',
            'status' => 'published',
            'is_featured' => true,
        ]);

        $response->assertRedirect(route('admin.projects.index'));
        $project->refresh();
        $this->assertSame('New Name', $project->title);
        $this->assertSame('published', $project->status);
        $this->assertTrue($project->is_featured);
    }

    public function test_editor_can_delete_a_project(): void
    {
        $editor = $this->editor();
        $project = Project::factory()->create();

        $this->actingAs($editor)
            ->delete(route('admin.projects.destroy', $project))
            ->assertRedirect(route('admin.projects.index'));

        // Deleting soft-deletes (see ProjectTrashTest for the trash/restore/force-delete flow).
        $this->assertNull(Project::find($project->id));
        $this->assertSoftDeleted($project);
    }

    public function test_a_user_without_permissions_cannot_delete_a_project(): void
    {
        $user = $this->userWithoutPermissions();
        $project = Project::factory()->create();

        $this->actingAs($user)
            ->delete(route('admin.projects.destroy', $project))
            ->assertForbidden();

        $this->assertModelExists($project);
    }
}
