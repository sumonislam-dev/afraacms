<?php

namespace Tests\Feature\Admin;

use App\Models\Project;
use App\Models\ProjectCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesAdminUsers;
use Tests\TestCase;

class ProjectCategoryTest extends TestCase
{
    use CreatesAdminUsers, RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('admin.project-categories.index'))->assertRedirect(route('login'));
    }

    public function test_a_user_without_permissions_cannot_manage_categories(): void
    {
        $user = $this->userWithoutPermissions();

        $this->actingAs($user)->get(route('admin.project-categories.index'))->assertForbidden();
    }

    public function test_editor_can_create_a_category(): void
    {
        $editor = $this->editor();

        $response = $this->actingAs($editor)->post(route('admin.project-categories.store'), [
            'name' => 'Residential',
            'slug' => 'residential',
        ]);

        $response->assertRedirect(route('admin.project-categories.index'));
        $this->assertDatabaseHas('project_categories', ['slug' => 'residential']);
    }

    public function test_a_category_slug_must_be_unique(): void
    {
        $editor = $this->editor();
        ProjectCategory::factory()->create(['slug' => 'commercial']);

        $this->actingAs($editor)
            ->post(route('admin.project-categories.store'), ['name' => 'Commercial', 'slug' => 'commercial'])
            ->assertSessionHasErrors('slug');
    }

    public function test_editor_can_update_a_category(): void
    {
        $editor = $this->editor();
        $category = ProjectCategory::factory()->create(['name' => 'Old Name']);

        $this->actingAs($editor)->put(route('admin.project-categories.update', $category), [
            'name' => 'New Name',
            'slug' => $category->slug,
        ])->assertRedirect(route('admin.project-categories.index'));

        $this->assertSame('New Name', $category->fresh()->name);
    }

    public function test_editor_can_delete_an_empty_category(): void
    {
        $editor = $this->editor();
        $category = ProjectCategory::factory()->create();

        $this->actingAs($editor)
            ->delete(route('admin.project-categories.destroy', $category))
            ->assertRedirect(route('admin.project-categories.index'));

        $this->assertModelMissing($category);
    }

    public function test_deleting_a_category_does_not_delete_its_projects(): void
    {
        $editor = $this->editor();
        $category = ProjectCategory::factory()->create();
        $project = Project::factory()->create(['category_id' => $category->id]);

        $this->actingAs($editor)->delete(route('admin.project-categories.destroy', $category));

        $this->assertModelExists($project->fresh());
        $this->assertNull($project->fresh()->category_id);
    }
}
