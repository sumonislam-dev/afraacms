<?php

namespace Tests\Feature;

use App\CMS\Services\SettingService;
use App\Models\Project;
use App\Models\Setting;
use App\Models\Story;
use App\Models\StoryCategory;
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

    public function test_the_stories_index_can_filter_by_category(): void
    {
        $health = StoryCategory::factory()->create(['name' => 'Health', 'slug' => 'health']);
        $education = StoryCategory::factory()->create(['name' => 'Education', 'slug' => 'education']);
        Story::factory()->published()->create(['title' => 'Health Story', 'category_id' => $health->id]);
        Story::factory()->published()->create(['title' => 'Education Story', 'category_id' => $education->id]);

        $response = $this->get(route('stories.index', ['category' => 'health']));

        $response->assertOk()->assertSee('Health Story')->assertDontSee('Education Story');
    }

    public function test_the_stories_index_can_be_searched(): void
    {
        Story::factory()->published()->create(['title' => 'Clean Water for Everyone']);
        Story::factory()->published()->create(['title' => 'A New School Opens']);

        $response = $this->get(route('stories.index', ['q' => 'water']));

        $response->assertOk()->assertSee('Clean Water for Everyone')->assertDontSee('A New School Opens');
    }

    public function test_the_stories_index_paginates_results(): void
    {
        Setting::updateOrCreate(['key' => 'stories_items_per_page'], ['value' => '1', 'group' => 'stories']);
        app(SettingService::class)->forget();

        Story::factory()->published()->create(['title' => 'First Story', 'published_at' => now()]);
        Story::factory()->published()->create(['title' => 'Second Story', 'published_at' => now()->subDay()]);

        $pageOne = $this->get(route('stories.index'));
        $pageOne->assertOk()->assertSee('First Story')->assertDontSee('Second Story');

        $pageTwo = $this->get(route('stories.index', ['page' => 2]));
        $pageTwo->assertOk()->assertSee('Second Story')->assertDontSee('First Story');
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
