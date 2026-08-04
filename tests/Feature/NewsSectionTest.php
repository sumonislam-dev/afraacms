<?php

namespace Tests\Feature;

use App\Models\NewsPost;
use App\Models\Page;
use App\Models\Section;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsSectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_news_section_shows_the_latest_published_posts(): void
    {
        NewsPost::factory()->published()->create(['title' => 'Latest Announcement']);
        NewsPost::factory()->create(['title' => 'Unpublished Draft']);

        $page = Page::factory()->create(['slug' => 'home', 'status' => 'published']);
        Section::factory()->for($page)->create(['type' => 'news', 'heading' => 'Latest News']);

        $response = $this->get('/'.$page->slug);

        $response->assertOk()
            ->assertSee('Latest News')
            ->assertSee('Latest Announcement')
            ->assertDontSee('Unpublished Draft');
    }
}
