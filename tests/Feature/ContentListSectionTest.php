<?php

namespace Tests\Feature;

use App\Models\NewsCategory;
use App\Models\NewsPost;
use App\Models\Page;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Section;
use App\Models\Story;
use App\Models\StoryCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ContentListSectionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
    }

    public function test_a_content_list_section_shows_all_active_news(): void
    {
        NewsPost::factory()->published()->create(['title' => 'Latest News Item']);
        NewsPost::factory()->create(['title' => 'Draft News Item']);

        $page = Page::factory()->create(['slug' => 'home', 'status' => 'published']);
        Section::factory()->for($page)->create(['type' => 'content_list', 'source' => 'news', 'heading' => 'News Feed']);

        $response = $this->get('/'.$page->slug);

        $response->assertOk()->assertSee('News Feed')->assertSee('Latest News Item')->assertDontSee('Draft News Item');
    }

    public function test_a_content_list_section_can_filter_news_by_category(): void
    {
        $events = NewsCategory::factory()->create();
        $press = NewsCategory::factory()->create();
        NewsPost::factory()->published()->create(['title' => 'Event Update', 'category_id' => $events->id]);
        NewsPost::factory()->published()->create(['title' => 'Press Release', 'category_id' => $press->id]);

        $page = Page::factory()->create(['slug' => 'home', 'status' => 'published']);
        $section = Section::factory()->for($page)->create(['type' => 'content_list', 'source' => 'news']);
        $section->newsCategories()->sync([$events->id]);

        $response = $this->get('/'.$page->slug);

        $response->assertOk()->assertSee('Event Update')->assertDontSee('Press Release');
    }

    /**
     * "Specific Items" is never silently truncated - unlike All Active/By
     * Category it isn't capped at item_limit, but item_limit still sets
     * the page size so a long hand-picked list paginates instead of all
     * rendering on one page.
     */
    public function test_a_content_list_section_can_show_specific_stories_uncapped_but_paginated(): void
    {
        $stories = collect([
            Story::factory()->published()->create(['title' => 'First Story', 'published_at' => now()]),
            Story::factory()->published()->create(['title' => 'Second Story', 'published_at' => now()->subDay()]),
            Story::factory()->published()->create(['title' => 'Third Story', 'published_at' => now()->subDays(2)]),
        ]);

        $page = Page::factory()->create(['slug' => 'home', 'status' => 'published']);
        $section = Section::factory()->for($page)->create([
            'type' => 'content_list',
            'source' => 'stories',
            'item_limit' => 1,
        ]);
        $section->storyItems()->sync($stories->pluck('id')->all());

        $pageName = 'content_page_'.$section->id;

        foreach ($stories as $index => $story) {
            $response = $this->get('/'.$page->slug.'?'.$pageName.'='.($index + 1));
            $response->assertOk()->assertSee($story->title);
        }
    }

    /**
     * The site-wide footer always links every published project by title
     * (see resources/views/layouts/frontend.blade.php), regardless of what
     * a page's own sections show - so these two check excerpt text, which
     * only appears in the section's own cards.
     */
    public function test_a_content_list_section_respects_the_item_limit_for_all_active_projects(): void
    {
        Project::factory()->published()->create(['title' => 'Newest Project', 'excerpt' => 'The newest excerpt', 'created_at' => now()]);
        Project::factory()->published()->create(['title' => 'Oldest Project', 'excerpt' => 'The oldest excerpt', 'created_at' => now()->subDay()]);

        $page = Page::factory()->create(['slug' => 'home', 'status' => 'published']);
        Section::factory()->for($page)->create(['type' => 'content_list', 'source' => 'projects', 'item_limit' => 1]);

        $response = $this->get('/'.$page->slug);

        $response->assertOk()->assertSee('The newest excerpt')->assertDontSee('The oldest excerpt');
    }

    public function test_a_content_list_section_can_filter_projects_by_category(): void
    {
        $water = ProjectCategory::factory()->create();
        $education = ProjectCategory::factory()->create();
        Project::factory()->published()->create(['title' => 'Water Project', 'excerpt' => 'Clean water for villages', 'category_id' => $water->id]);
        Project::factory()->published()->create(['title' => 'Education Project', 'excerpt' => 'Books for schools', 'category_id' => $education->id]);

        $page = Page::factory()->create(['slug' => 'home', 'status' => 'published']);
        $section = Section::factory()->for($page)->create(['type' => 'content_list', 'source' => 'projects']);
        $section->projectCategories()->sync([$water->id]);

        $response = $this->get('/'.$page->slug);

        $response->assertOk()->assertSee('Clean water for villages')->assertDontSee('Books for schools');
    }

    public function test_a_content_list_section_can_filter_stories_by_category(): void
    {
        $health = StoryCategory::factory()->create();
        $education = StoryCategory::factory()->create();
        Story::factory()->published()->create(['title' => 'Health Win', 'category_id' => $health->id]);
        Story::factory()->published()->create(['title' => 'Education Win', 'category_id' => $education->id]);

        $page = Page::factory()->create(['slug' => 'home', 'status' => 'published']);
        $section = Section::factory()->for($page)->create(['type' => 'content_list', 'source' => 'stories']);
        $section->storyCategories()->sync([$health->id]);

        $response = $this->get('/'.$page->slug);

        $response->assertOk()->assertSee('Health Win')->assertDontSee('Education Win');
    }

    public function test_a_content_list_section_shows_only_notices_for_the_notices_source(): void
    {
        $withAttachment = NewsPost::factory()->published()->create(['title' => 'Has Attachment']);
        $withAttachment->addMedia(UploadedFile::fake()->create('file.pdf', 10))->toMediaCollection('attachment');
        NewsPost::factory()->published()->create(['title' => 'No Attachment']);

        $page = Page::factory()->create(['slug' => 'home', 'status' => 'published']);
        Section::factory()->for($page)->create(['type' => 'content_list', 'source' => 'notices']);

        $response = $this->get('/'.$page->slug);

        $response->assertOk()->assertSee('Has Attachment')->assertDontSee('No Attachment');
    }

    public function test_a_content_list_section_can_render_as_a_table(): void
    {
        NewsPost::factory()->published()->create(['title' => 'Table Row News']);

        $page = Page::factory()->create(['slug' => 'home', 'status' => 'published']);
        Section::factory()->for($page)->create(['type' => 'content_list', 'source' => 'news', 'layout' => 'table']);

        $response = $this->get('/'.$page->slug);

        $response->assertOk()->assertSee('Table Row News')->assertSeeInOrder(['S.L', 'Title', 'Description']);
    }

    public function test_a_content_list_section_shows_a_search_box_when_enabled(): void
    {
        Story::factory()->published()->create(['title' => 'Searchable Story']);

        $page = Page::factory()->create(['slug' => 'home', 'status' => 'published']);
        Section::factory()->for($page)->create(['type' => 'content_list', 'source' => 'stories', 'show_search' => true]);

        $response = $this->get('/'.$page->slug);

        $response->assertOk()->assertSee('Search Success Stories...');
    }

    /**
     * The search box submits in place (to the section's own page, not the
     * dedicated /stories index) so results filter without navigating away -
     * see resources/js/app.js's AJAX enhancement for the no-JS-disabled case.
     */
    public function test_a_content_list_sections_search_box_filters_its_own_items_in_place(): void
    {
        Story::factory()->published()->create(['title' => 'Clean Water Initiative']);
        Story::factory()->published()->create(['title' => 'Education for All']);

        $page = Page::factory()->create(['slug' => 'home', 'status' => 'published']);
        $section = Section::factory()->for($page)->create(['type' => 'content_list', 'source' => 'stories', 'show_search' => true]);
        $qName = 'content_q_'.$section->id;

        $response = $this->get('/'.$page->slug.'?'.$qName.'=water');

        $response->assertOk()
            ->assertSee('Clean Water Initiative')
            ->assertDontSee('Education for All')
            ->assertSee('/'.$page->slug, false);
    }

    /**
     * A search with zero matches keeps the section (and its search box)
     * visible with a "no results" message, instead of the whole section
     * disappearing the way it does when nothing is configured at all.
     */
    public function test_a_content_list_sections_search_with_no_matches_shows_a_message_instead_of_hiding(): void
    {
        Story::factory()->published()->create(['title' => 'Clean Water Initiative']);

        $page = Page::factory()->create(['slug' => 'home', 'status' => 'published']);
        $section = Section::factory()->for($page)->create(['type' => 'content_list', 'source' => 'stories', 'show_search' => true]);
        $qName = 'content_q_'.$section->id;

        $response = $this->get('/'.$page->slug.'?'.$qName.'=nonexistentkeyword');

        $response->assertOk()
            ->assertSee('Search Success Stories...')
            ->assertSee('No Success Stories found for &quot;nonexistentkeyword&quot;.', false)
            ->assertDontSee('Clean Water Initiative');
    }

    public function test_a_content_list_section_shows_a_default_view_all_button(): void
    {
        Story::factory()->published()->create(['title' => 'A Story']);

        $page = Page::factory()->create(['slug' => 'home', 'status' => 'published']);
        Section::factory()->for($page)->create(['type' => 'content_list', 'source' => 'stories']);

        $response = $this->get('/'.$page->slug);

        $response->assertOk()->assertSee('View All Stories')->assertSee(route('stories.index'), false);
    }

    public function test_a_content_list_sections_button_can_be_overridden(): void
    {
        NewsPost::factory()->published()->create(['title' => 'A Post']);

        $page = Page::factory()->create(['slug' => 'home', 'status' => 'published']);
        Section::factory()->for($page)->create([
            'type' => 'content_list',
            'source' => 'news',
            'button_text' => 'See Every Update',
            'button_url' => '/custom-news-link',
        ]);

        $response = $this->get('/'.$page->slug);

        $response->assertOk()->assertSee('See Every Update')->assertSee('/custom-news-link', false);
    }

    public function test_a_content_list_section_with_no_source_renders_nothing(): void
    {
        NewsPost::factory()->published()->create(['title' => 'Should Not Appear In This Section']);

        $page = Page::factory()->create(['slug' => 'home', 'status' => 'published']);
        Section::factory()->for($page)->create(['type' => 'content_list', 'source' => null, 'heading' => 'Empty Content List']);

        $response = $this->get('/'.$page->slug);

        $response->assertOk()->assertDontSee('Empty Content List');
    }

    public function test_a_content_list_section_paginates_all_active_items(): void
    {
        Project::factory()->published()->create(['title' => 'Newest Project', 'created_at' => now()]);
        Project::factory()->published()->create(['title' => 'Oldest Project', 'created_at' => now()->subDay()]);

        $page = Page::factory()->create(['slug' => 'home', 'status' => 'published']);
        $section = Section::factory()->for($page)->create(['type' => 'content_list', 'source' => 'projects', 'item_limit' => 1]);
        $pageName = 'content_page_'.$section->id;

        $pageOne = $this->get('/'.$page->slug);
        $pageOne->assertOk()
            ->assertSee('1 of 2')
            ->assertSee('Newest Project')
            ->assertSee("?{$pageName}=2", false);

        $pageTwo = $this->get('/'.$page->slug.'?'.$pageName.'=2');
        $pageTwo->assertOk()->assertSee('2 of 2');
    }

    public function test_two_content_list_sections_on_one_page_paginate_independently(): void
    {
        NewsPost::factory()->published()->create(['title' => 'Newest News', 'published_at' => now()]);
        NewsPost::factory()->published()->create(['title' => 'Oldest News', 'published_at' => now()->subDay()]);
        Story::factory()->published()->create(['title' => 'Newest Story', 'published_at' => now()]);
        Story::factory()->published()->create(['title' => 'Oldest Story', 'published_at' => now()->subDay()]);

        $page = Page::factory()->create(['slug' => 'home', 'status' => 'published']);
        $newsSection = Section::factory()->for($page)->create(['type' => 'content_list', 'source' => 'news', 'item_limit' => 1, 'sort_order' => 0]);
        $storySection = Section::factory()->for($page)->create(['type' => 'content_list', 'source' => 'stories', 'item_limit' => 1, 'sort_order' => 1]);

        $response = $this->get('/'.$page->slug.'?content_page_'.$newsSection->id.'=2');

        $response->assertOk()
            ->assertSee('Oldest News')
            ->assertDontSee('Newest News')
            ->assertSee('Newest Story')
            ->assertDontSee('Oldest Story');
    }
}
