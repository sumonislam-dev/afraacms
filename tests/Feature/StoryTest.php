<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Story;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_stories_index_lists_only_published_stories(): void
    {
        Story::factory()->published()->create(['title' => 'Published Story']);
        Story::factory()->create(['title' => 'Draft Story']);

        $response = $this->get(route('stories.index'));

        $response->assertOk()->assertSee('Published Story')->assertDontSee('Draft Story');
    }

    public function test_the_stories_index_can_filter_by_project(): void
    {
        $training = Project::factory()->create(['title' => 'Training Project', 'slug' => 'training-project']);
        $water = Project::factory()->create(['title' => 'Water Project', 'slug' => 'water-project']);
        Story::factory()->published()->create(['title' => 'Training Story', 'project_id' => $training->id]);
        Story::factory()->published()->create(['title' => 'Water Story', 'project_id' => $water->id]);

        $response = $this->get(route('stories.index', ['project' => 'training-project']));

        $response->assertOk()->assertSee('Training Story')->assertDontSee('Water Story');
    }

    public function test_a_published_story_has_a_working_show_page(): void
    {
        $story = Story::factory()->published()->create([
            'title' => 'Scholarship Success',
            'slug' => 'scholarship-success',
            'excerpt' => 'Great news for students.',
        ]);

        $response = $this->get(route('stories.show', $story->slug));

        $response->assertOk()->assertSee('Scholarship Success')->assertSee('Great news for students.');
    }

    public function test_a_draft_story_returns_404(): void
    {
        $story = Story::factory()->create(['slug' => 'draft-story']);

        $this->get(route('stories.show', $story->slug))->assertNotFound();
    }

    public function test_an_unknown_slug_returns_404(): void
    {
        $this->get(route('stories.show', 'does-not-exist'))->assertNotFound();
    }
}
