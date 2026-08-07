<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Story;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_project_show_page_lists_its_related_success_stories(): void
    {
        $project = Project::factory()->create(['status' => 'published', 'slug' => 'water-project']);
        $otherProject = Project::factory()->create(['status' => 'published', 'slug' => 'other-project']);

        Story::factory()->published()->create(['title' => 'Clean Water Changed Everything', 'project_id' => $project->id]);
        Story::factory()->published()->create(['title' => 'An Unrelated Story', 'project_id' => $otherProject->id]);

        $response = $this->get(route('projects.show', $project->slug));

        $response->assertOk()
            ->assertSee('Clean Water Changed Everything')
            ->assertDontSee('An Unrelated Story');
    }

    public function test_a_project_show_page_with_no_stories_does_not_show_the_stories_heading(): void
    {
        $project = Project::factory()->create(['status' => 'published', 'slug' => 'no-stories-project']);

        $response = $this->get(route('projects.show', $project->slug));

        $response->assertOk()->assertDontSee('Success Stories From This Project');
    }
}
