<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\Section;
use App\Models\TeamCategory;
use App\Models\TeamMember;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamDirectorySectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_shows_every_active_member_when_none_are_hand_picked(): void
    {
        TeamMember::factory()->create(['name' => 'Jane Doe']);
        TeamMember::factory()->create(['name' => 'John Smith', 'is_active' => false]);

        $page = Page::factory()->create(['slug' => 'about', 'status' => 'published']);
        Section::factory()->for($page)->create(['type' => 'team_members', 'heading' => 'Our Team']);

        $response = $this->get('/'.$page->slug);

        $response->assertOk()->assertSee('Jane Doe')->assertDontSee('John Smith');
    }

    public function test_it_shows_only_the_chosen_category(): void
    {
        $volunteers = TeamCategory::factory()->create(['name' => 'Volunteers']);
        $staff = TeamCategory::factory()->create(['name' => 'Staff']);
        TeamMember::factory()->create(['name' => 'Volunteer Val', 'category_id' => $volunteers->id]);
        TeamMember::factory()->create(['name' => 'Staffer Stan', 'category_id' => $staff->id]);

        $page = Page::factory()->create(['slug' => 'volunteers', 'status' => 'published']);
        $section = Section::factory()->for($page)->create(['type' => 'team_members', 'heading' => 'Volunteers']);
        $section->teamCategories()->sync([$volunteers->id]);

        $response = $this->get('/'.$page->slug);

        $response->assertOk()->assertSee('Volunteer Val')->assertDontSee('Staffer Stan');
    }

    public function test_it_shows_only_hand_picked_members(): void
    {
        $picked = TeamMember::factory()->create(['name' => 'Picked Pete']);
        TeamMember::factory()->create(['name' => 'Skipped Sam']);

        $page = Page::factory()->create(['slug' => 'board', 'status' => 'published']);
        $section = Section::factory()->for($page)->create(['type' => 'team_members', 'heading' => 'Board']);
        $section->teamMembers()->sync([$picked->id]);

        $response = $this->get('/'.$page->slug);

        $response->assertOk()->assertSee('Picked Pete')->assertDontSee('Skipped Sam');
    }
}
