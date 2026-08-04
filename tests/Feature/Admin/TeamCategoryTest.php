<?php

namespace Tests\Feature\Admin;

use App\Models\TeamCategory;
use App\Models\TeamMember;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesAdminUsers;
use Tests\TestCase;

class TeamCategoryTest extends TestCase
{
    use CreatesAdminUsers, RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('admin.team-categories.index'))->assertRedirect(route('login'));
    }

    public function test_a_user_without_permissions_cannot_manage_categories(): void
    {
        $user = $this->userWithoutPermissions();

        $this->actingAs($user)->get(route('admin.team-categories.index'))->assertForbidden();
    }

    public function test_editor_can_create_a_category(): void
    {
        $editor = $this->editor();

        $response = $this->actingAs($editor)->post(route('admin.team-categories.store'), [
            'name' => 'Volunteers',
            'slug' => 'volunteers',
        ]);

        $response->assertRedirect(route('admin.team-categories.index'));
        $this->assertDatabaseHas('team_categories', ['slug' => 'volunteers']);
    }

    public function test_a_category_slug_must_be_unique(): void
    {
        $editor = $this->editor();
        TeamCategory::factory()->create(['slug' => 'staff']);

        $this->actingAs($editor)
            ->post(route('admin.team-categories.store'), ['name' => 'Staff', 'slug' => 'staff'])
            ->assertSessionHasErrors('slug');
    }

    public function test_editor_can_update_a_category(): void
    {
        $editor = $this->editor();
        $category = TeamCategory::factory()->create(['name' => 'Old Name']);

        $this->actingAs($editor)->put(route('admin.team-categories.update', $category), [
            'name' => 'New Name',
            'slug' => $category->slug,
        ])->assertRedirect(route('admin.team-categories.index'));

        $this->assertSame('New Name', $category->fresh()->name);
    }

    public function test_editor_can_delete_an_empty_category(): void
    {
        $editor = $this->editor();
        $category = TeamCategory::factory()->create();

        $this->actingAs($editor)
            ->delete(route('admin.team-categories.destroy', $category))
            ->assertRedirect(route('admin.team-categories.index'));

        $this->assertModelMissing($category);
    }

    public function test_deleting_a_category_does_not_delete_its_members(): void
    {
        $editor = $this->editor();
        $category = TeamCategory::factory()->create();
        $member = TeamMember::factory()->create(['category_id' => $category->id]);

        $this->actingAs($editor)->delete(route('admin.team-categories.destroy', $category));

        $this->assertModelExists($member->fresh());
        $this->assertNull($member->fresh()->category_id);
    }
}
