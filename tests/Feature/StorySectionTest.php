<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\Section;
use App\Models\Story;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorySectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_stories_section_shows_the_latest_published_stories(): void
    {
        Story::factory()->published()->create(['title' => 'Featured Success Story']);
        Story::factory()->create(['title' => 'Unpublished Draft Story']);

        $page = Page::factory()->create(['slug' => 'home', 'status' => 'published']);
        Section::factory()->for($page)->create(['type' => 'stories', 'heading' => 'Lives Changed Through RSUF']);

        $response = $this->get('/'.$page->slug);

        $response->assertOk()
            ->assertSee('Lives Changed Through RSUF')
            ->assertSee('Featured Success Story')
            ->assertDontSee('Unpublished Draft Story');
    }
}
