<?php

namespace Tests\Feature\Admin;

use App\Models\Gallery;
use App\Models\Page;
use App\Models\Section;
use App\Models\TeamCategory;
use App\Models\TeamMember;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesAdminUsers;
use Tests\TestCase;

class SectionTest extends TestCase
{
    use CreatesAdminUsers, RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $page = Page::factory()->create();

        $this->get(route('admin.pages.sections.index', $page))->assertRedirect(route('login'));
        $this->get(route('admin.pages.sections.create', $page))->assertRedirect(route('login'));
    }

    public function test_a_user_without_permissions_cannot_manage_sections(): void
    {
        $user = $this->userWithoutPermissions();
        $page = Page::factory()->create();

        $this->actingAs($user)->get(route('admin.pages.sections.index', $page))->assertForbidden();
        $this->actingAs($user)->get(route('admin.pages.sections.create', $page))->assertForbidden();
    }

    public function test_editor_can_view_a_pages_sections(): void
    {
        $editor = $this->editor();
        $page = Page::factory()->create();
        Section::factory()->for($page)->create(['heading' => 'Welcome Hero']);

        $this->actingAs($editor)
            ->get(route('admin.pages.sections.index', $page))
            ->assertOk()
            ->assertSee('Welcome Hero');
    }

    public function test_editor_can_add_a_section_to_a_page(): void
    {
        $editor = $this->editor();
        $page = Page::factory()->create();

        $response = $this->actingAs($editor)->post(route('admin.pages.sections.store', $page), [
            'type' => 'hero',
            'heading' => 'New Section',
            'subheading' => 'Subheading text',
        ]);

        $section = Section::first();

        $response->assertRedirect(route('admin.pages.sections.edit', [$page, $section]));
        $this->assertDatabaseHas('sections', [
            'page_id' => $page->id,
            'heading' => 'New Section',
            'type' => 'hero',
        ]);
    }

    public function test_a_section_type_must_be_a_known_type(): void
    {
        $editor = $this->editor();
        $page = Page::factory()->create();

        $this->actingAs($editor)
            ->post(route('admin.pages.sections.store', $page), ['type' => 'not-a-real-type'])
            ->assertSessionHasErrors('type');
    }

    public function test_editor_can_update_a_section(): void
    {
        $editor = $this->editor();
        $page = Page::factory()->create();
        $section = Section::factory()->for($page)->create(['heading' => 'Old Heading']);

        $response = $this->actingAs($editor)->put(route('admin.pages.sections.update', [$page, $section]), [
            'type' => 'hero',
            'heading' => 'Updated Heading',
        ]);

        $response->assertRedirect(route('admin.pages.sections.edit', [$page, $section]));
        $this->assertSame('Updated Heading', $section->fresh()->heading);
    }

    public function test_editor_can_delete_a_section(): void
    {
        $editor = $this->editor();
        $page = Page::factory()->create();
        $section = Section::factory()->for($page)->create();

        $this->actingAs($editor)
            ->delete(route('admin.pages.sections.destroy', [$page, $section]))
            ->assertRedirect(route('admin.pages.sections.index', $page));

        $this->assertModelMissing($section);
    }

    public function test_a_user_without_permissions_cannot_delete_a_section(): void
    {
        $user = $this->userWithoutPermissions();
        $page = Page::factory()->create();
        $section = Section::factory()->for($page)->create();

        $this->actingAs($user)
            ->delete(route('admin.pages.sections.destroy', [$page, $section]))
            ->assertForbidden();

        $this->assertModelExists($section);
    }

    public function test_editor_can_select_specific_galleries_for_a_gallery_albums_section(): void
    {
        $editor = $this->editor();
        $page = Page::factory()->create();
        $gallery = Gallery::create([
            'title' => 'Test Album',
            'slug' => 'test-album',
            'is_active' => true,
            'sort_order' => 0,
        ]);

        $response = $this->actingAs($editor)->post(route('admin.pages.sections.store', $page), [
            'type' => 'gallery_albums',
            'heading' => 'Our Work',
            'galleries' => [$gallery->id],
        ]);

        $section = Section::first();
        $response->assertRedirect(route('admin.pages.sections.edit', [$page, $section]));
        $this->assertSame([$gallery->id], $section->galleries()->pluck('galleries.id')->all());
    }

    public function test_editor_can_add_a_team_section_with_a_member(): void
    {
        $editor = $this->editor();
        $page = Page::factory()->create();

        $this->actingAs($editor)->post(route('admin.pages.sections.store', $page), [
            'type' => 'team',
            'heading' => 'Our Team',
        ])->assertRedirect();

        $section = Section::first();

        $this->actingAs($editor)->post(route('admin.pages.sections.items.store', [$page, $section]), [
            'title' => 'Jane Doe',
            'subtitle' => 'Executive Director',
            'body' => 'Leads the organisation.',
        ])->assertRedirect();

        $this->assertDatabaseHas('section_items', [
            'section_id' => $section->id,
            'title' => 'Jane Doe',
            'subtitle' => 'Executive Director',
        ]);
    }

    public function test_editor_can_pick_specific_team_members_for_a_team_directory_section(): void
    {
        $editor = $this->editor();
        $page = Page::factory()->create();
        $member = TeamMember::factory()->create();

        $response = $this->actingAs($editor)->post(route('admin.pages.sections.store', $page), [
            'type' => 'team_members',
            'heading' => 'Our Volunteers',
            'team_members' => [$member->id],
        ]);

        $section = Section::first();
        $response->assertRedirect(route('admin.pages.sections.edit', [$page, $section]));
        $this->assertSame([$member->id], $section->teamMembers()->pluck('team_members.id')->all());
    }

    public function test_editor_can_pick_a_team_category_for_a_team_directory_section(): void
    {
        $editor = $this->editor();
        $page = Page::factory()->create();
        $category = TeamCategory::factory()->create();

        $response = $this->actingAs($editor)->post(route('admin.pages.sections.store', $page), [
            'type' => 'team_members',
            'heading' => 'Volunteers',
            'team_category_ids' => [$category->id],
        ]);

        $section = Section::first();
        $response->assertRedirect(route('admin.pages.sections.edit', [$page, $section]));
        $this->assertSame([$category->id], $section->teamCategories()->pluck('team_categories.id')->all());
    }
}
