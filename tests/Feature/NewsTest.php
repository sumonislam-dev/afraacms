<?php

namespace Tests\Feature;

use App\Models\NewsCategory;
use App\Models\NewsPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_news_index_lists_only_published_posts(): void
    {
        NewsPost::factory()->published()->create(['title' => 'Published Post']);
        NewsPost::factory()->create(['title' => 'Draft Post']);

        $response = $this->get(route('news.index'));

        $response->assertOk()->assertSee('Published Post')->assertDontSee('Draft Post');
    }

    public function test_the_news_index_can_filter_by_category(): void
    {
        $events = NewsCategory::factory()->create(['name' => 'Events', 'slug' => 'events']);
        $press = NewsCategory::factory()->create(['name' => 'Press', 'slug' => 'press']);
        NewsPost::factory()->published()->create(['title' => 'Event Post', 'category_id' => $events->id]);
        NewsPost::factory()->published()->create(['title' => 'Press Post', 'category_id' => $press->id]);

        $response = $this->get(route('news.index', ['category' => 'events']));

        $response->assertOk()->assertSee('Event Post')->assertDontSee('Press Post');
    }

    public function test_a_published_post_has_a_working_show_page(): void
    {
        $post = NewsPost::factory()->published()->create([
            'title' => 'Scholarship Update',
            'slug' => 'scholarship-update',
            'excerpt' => 'Great news for students.',
        ]);

        $response = $this->get(route('news.show', $post->slug));

        $response->assertOk()->assertSee('Scholarship Update')->assertSee('Great news for students.');
    }

    public function test_a_draft_post_returns_404(): void
    {
        $post = NewsPost::factory()->create(['slug' => 'draft-post']);

        $this->get(route('news.show', $post->slug))->assertNotFound();
    }

    public function test_an_unknown_slug_returns_404(): void
    {
        $this->get(route('news.show', 'does-not-exist'))->assertNotFound();
    }
}
