<?php

namespace Tests\Feature;

use App\CMS\Services\SettingService;
use App\Models\NewsPost;
use App\Models\Page;
use App\Models\Section;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsSectionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A fresh RefreshDatabase test has no Setting rows at all (no seeder
     * runs automatically), so this must create the row rather than assume
     * it exists - and bust SettingService's own cache afterward, since
     * writing to the model directly bypasses its cache invalidation.
     */
    private function setNewsSectionCount(int $count): void
    {
        Setting::updateOrCreate(['key' => 'news_section_count'], ['value' => (string) $count, 'group' => 'news']);
        app(SettingService::class)->forget();
    }

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

    public function test_a_news_section_respects_the_news_section_count_setting(): void
    {
        $this->setNewsSectionCount(2);

        NewsPost::factory()->published()->create(['title' => 'Newest Post', 'published_at' => now()]);
        NewsPost::factory()->published()->create(['title' => 'Middle Post', 'published_at' => now()->subDay()]);
        NewsPost::factory()->published()->create(['title' => 'Oldest Post', 'published_at' => now()->subDays(2)]);

        $page = Page::factory()->create(['slug' => 'home', 'status' => 'published']);
        Section::factory()->for($page)->create(['type' => 'news']);

        $response = $this->get('/'.$page->slug);

        $response->assertOk()
            ->assertSee('Newest Post')
            ->assertSee('Middle Post')
            ->assertDontSee('Oldest Post');
    }
}
