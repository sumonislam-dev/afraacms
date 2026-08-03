<?php

namespace Tests\Feature\Admin;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesAdminUsers;
use Tests\TestCase;

class ProjectTrashTest extends TestCase
{
    use CreatesAdminUsers, RefreshDatabase;

    public function test_deleting_a_project_soft_deletes_it(): void
    {
        $editor = $this->editor();
        $project = Project::factory()->create();

        $this->actingAs($editor)->delete(route('admin.projects.destroy', $project));

        $this->assertNull(Project::find($project->id));
        $trashed = Project::withTrashed()->find($project->id);
        $this->assertNotNull($trashed);
        $this->assertNotNull($trashed->deleted_at);
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('admin.projects.trash'))->assertRedirect(route('login'));
    }

    public function test_a_user_without_permissions_cannot_view_trash(): void
    {
        $user = $this->userWithoutPermissions();

        $this->actingAs($user)->get(route('admin.projects.trash'))->assertForbidden();
    }

    public function test_the_trash_lists_only_deleted_projects(): void
    {
        $editor = $this->editor();
        Project::factory()->create(['title' => 'Still Active']);
        $trashed = Project::factory()->create(['title' => 'Removed Project']);
        $trashed->delete();

        $response = $this->actingAs($editor)->get(route('admin.projects.trash'));

        $response->assertOk()->assertSee('Removed Project')->assertDontSee('Still Active');
    }

    public function test_editor_can_restore_a_trashed_project(): void
    {
        $editor = $this->editor();
        $project = Project::factory()->create();
        $project->delete();

        $this->actingAs($editor)
            ->post(route('admin.projects.restore', $project))
            ->assertRedirect(route('admin.projects.trash'));

        $this->assertNotNull(Project::find($project->id));
    }

    public function test_a_user_without_permissions_cannot_restore_a_project(): void
    {
        $user = $this->userWithoutPermissions();
        $project = Project::factory()->create();
        $project->delete();

        $this->actingAs($user)
            ->post(route('admin.projects.restore', $project))
            ->assertForbidden();

        $this->assertNull(Project::find($project->id));
    }

    public function test_editor_can_permanently_delete_a_trashed_project(): void
    {
        $editor = $this->editor();
        $project = Project::factory()->create();
        $project->delete();

        $this->actingAs($editor)
            ->delete(route('admin.projects.force-delete', $project))
            ->assertRedirect(route('admin.projects.trash'));

        $this->assertNull(Project::withTrashed()->find($project->id));
    }

    public function test_a_user_without_permissions_cannot_permanently_delete_a_project(): void
    {
        $user = $this->userWithoutPermissions();
        $project = Project::factory()->create();
        $project->delete();

        $this->actingAs($user)
            ->delete(route('admin.projects.force-delete', $project))
            ->assertForbidden();

        $this->assertNotNull(Project::withTrashed()->find($project->id));
    }

    public function test_a_trashed_projects_slug_still_blocks_reuse_until_purged(): void
    {
        $editor = $this->editor();
        $project = Project::factory()->create(['slug' => 'riverside-tower']);
        $project->delete();

        $this->actingAs($editor)
            ->post(route('admin.projects.store'), [
                'title' => 'New Riverside Tower',
                'slug' => 'riverside-tower',
                'status' => 'draft',
            ])
            ->assertSessionHasErrors('slug');
    }
}
