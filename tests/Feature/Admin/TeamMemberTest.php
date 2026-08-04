<?php

namespace Tests\Feature\Admin;

use App\Models\TeamCategory;
use App\Models\TeamMember;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesAdminUsers;
use Tests\TestCase;

class TeamMemberTest extends TestCase
{
    use CreatesAdminUsers, RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $member = TeamMember::factory()->create();

        $this->get(route('admin.team.index'))->assertRedirect(route('login'));
        $this->get(route('admin.team.edit', $member))->assertRedirect(route('login'));
    }

    public function test_a_user_without_permissions_cannot_view_team(): void
    {
        $user = $this->userWithoutPermissions();

        $this->actingAs($user)->get(route('admin.team.index'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.team.create'))->assertForbidden();
    }

    public function test_editor_can_view_the_team_list(): void
    {
        $editor = $this->editor();
        TeamMember::factory()->create(['name' => 'Jane Doe']);

        $this->actingAs($editor)
            ->get(route('admin.team.index'))
            ->assertOk()
            ->assertSee('Jane Doe');
    }

    public function test_editor_can_add_a_team_member_with_a_category(): void
    {
        $editor = $this->editor();
        $category = TeamCategory::factory()->create();

        $response = $this->actingAs($editor)->post(route('admin.team.store'), [
            'category_id' => $category->id,
            'name' => 'Jane Doe',
            'role' => 'Executive Director',
        ]);

        $response->assertRedirect(route('admin.team.index'));
        $this->assertDatabaseHas('team_members', [
            'name' => 'Jane Doe',
            'category_id' => $category->id,
        ]);
    }

    public function test_a_user_without_permissions_cannot_add_a_team_member(): void
    {
        $user = $this->userWithoutPermissions();

        $this->actingAs($user)->post(route('admin.team.store'), [
            'name' => 'Sneaky Member',
        ])->assertForbidden();

        $this->assertDatabaseMissing('team_members', ['name' => 'Sneaky Member']);
    }

    public function test_adding_a_team_member_requires_a_name(): void
    {
        $editor = $this->editor();

        $this->actingAs($editor)
            ->post(route('admin.team.store'), ['role' => 'No Name'])
            ->assertSessionHasErrors('name');
    }

    public function test_a_team_members_category_must_exist(): void
    {
        $editor = $this->editor();

        $this->actingAs($editor)
            ->post(route('admin.team.store'), [
                'category_id' => 999999,
                'name' => 'Orphan Member',
            ])
            ->assertSessionHasErrors('category_id');
    }

    public function test_editor_can_update_a_team_member(): void
    {
        $editor = $this->editor();
        $member = TeamMember::factory()->create(['name' => 'Old Name']);

        $response = $this->actingAs($editor)->put(route('admin.team.update', $member), [
            'name' => 'New Name',
            'role' => 'Program Manager',
            'is_active' => false,
        ]);

        $response->assertRedirect(route('admin.team.index'));
        $member->refresh();
        $this->assertSame('New Name', $member->name);
        $this->assertFalse($member->is_active);
    }

    public function test_editor_can_delete_a_team_member(): void
    {
        $editor = $this->editor();
        $member = TeamMember::factory()->create();

        $this->actingAs($editor)
            ->delete(route('admin.team.destroy', $member))
            ->assertRedirect(route('admin.team.index'));

        $this->assertNull(TeamMember::find($member->id));
        $this->assertSoftDeleted($member);
    }

    public function test_a_user_without_permissions_cannot_delete_a_team_member(): void
    {
        $user = $this->userWithoutPermissions();
        $member = TeamMember::factory()->create();

        $this->actingAs($user)
            ->delete(route('admin.team.destroy', $member))
            ->assertForbidden();

        $this->assertModelExists($member);
    }

    public function test_editor_can_restore_a_trashed_team_member(): void
    {
        $editor = $this->editor();
        $member = TeamMember::factory()->create();
        $member->delete();

        $this->actingAs($editor)
            ->post(route('admin.team.restore', $member))
            ->assertRedirect(route('admin.team.trash'));

        $this->assertNotSoftDeleted($member);
    }
}
