<?php

namespace Tests\Feature;

use App\CMS\Services\SettingService;
use App\Models\NewsCategory;
use App\Models\NewsPost;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
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

    public function test_the_news_index_can_be_searched(): void
    {
        NewsPost::factory()->published()->create(['title' => 'New Scholarship Fund Launches']);
        NewsPost::factory()->published()->create(['title' => 'Volunteers Needed This Weekend']);

        $response = $this->get(route('news.index', ['q' => 'scholarship']));

        $response->assertOk()->assertSee('New Scholarship Fund Launches')->assertDontSee('Volunteers Needed This Weekend');
    }

    public function test_the_news_index_paginates_results(): void
    {
        Setting::updateOrCreate(['key' => 'news_items_per_page'], ['value' => '1', 'group' => 'news']);
        app(SettingService::class)->forget();

        NewsPost::factory()->published()->create(['title' => 'First Post', 'published_at' => now()]);
        NewsPost::factory()->published()->create(['title' => 'Second Post', 'published_at' => now()->subDay()]);

        $pageOne = $this->get(route('news.index'));
        $pageOne->assertOk()->assertSee('First Post')->assertDontSee('Second Post');

        $pageTwo = $this->get(route('news.index', ['page' => 2]));
        $pageTwo->assertOk()->assertSee('Second Post')->assertDontSee('First Post');
    }

    public function test_the_news_index_can_filter_to_only_notices(): void
    {
        $withAttachment = NewsPost::factory()->published()->create(['title' => 'Notice Post']);
        $withAttachment->addMedia(UploadedFile::fake()->create('notice.pdf', 50))->toMediaCollection('attachment');
        NewsPost::factory()->published()->create(['title' => 'Plain Article']);

        $response = $this->get(route('news.index', ['format' => 'notice']));

        $response->assertOk()->assertSee('Notice Post')->assertDontSee('Plain Article');
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
