<?php

namespace Tests\Feature;

use App\CMS\Services\SettingService;
use App\Models\Gallery;
use App\Models\MediaItem;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Setting;
use App\Models\Story;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_projects_index_lists_only_published_projects(): void
    {
        Project::factory()->published()->create(['title' => 'Published Project']);
        Project::factory()->create(['title' => 'Draft Project']);

        $response = $this->get(route('projects.index'));

        $response->assertOk()->assertSee('Published Project')->assertDontSee('Draft Project');
    }

    /**
     * The site-wide footer always links every published project by title
     * (see resources/views/layouts/frontend.blade.php), regardless of the
     * current listing's filter/search/page - so these assertions check the
     * excerpt text, which only ever appears in the main listing's cards.
     */
    public function test_the_projects_index_can_filter_by_category(): void
    {
        $water = ProjectCategory::factory()->create(['name' => 'Water', 'slug' => 'water']);
        $education = ProjectCategory::factory()->create(['name' => 'Education', 'slug' => 'education']);
        Project::factory()->published()->create(['title' => 'Water Project', 'excerpt' => 'Clean water for villages', 'category_id' => $water->id]);
        Project::factory()->published()->create(['title' => 'Education Project', 'excerpt' => 'Books for schools', 'category_id' => $education->id]);

        $response = $this->get(route('projects.index', ['category' => 'water']));

        $response->assertOk()->assertSee('Clean water for villages')->assertDontSee('Books for schools');
    }

    public function test_the_projects_index_can_be_searched(): void
    {
        Project::factory()->published()->create(['title' => 'Clean Water Initiative', 'excerpt' => 'Wells dug this year']);
        Project::factory()->published()->create(['title' => 'School Renovation', 'excerpt' => 'New roofs and desks']);

        $response = $this->get(route('projects.index', ['q' => 'water']));

        $response->assertOk()->assertSee('Wells dug this year')->assertDontSee('New roofs and desks');
    }

    public function test_the_projects_index_paginates_results(): void
    {
        Setting::updateOrCreate(['key' => 'projects_items_per_page'], ['value' => '1', 'group' => 'projects']);
        app(SettingService::class)->forget();

        Project::factory()->published()->create(['title' => 'First Project', 'excerpt' => 'The first excerpt', 'created_at' => now()]);
        Project::factory()->published()->create(['title' => 'Second Project', 'excerpt' => 'The second excerpt', 'created_at' => now()->subDay()]);

        $pageOne = $this->get(route('projects.index'));
        $pageOne->assertOk()->assertSee('The first excerpt')->assertDontSee('The second excerpt');

        $pageTwo = $this->get(route('projects.index', ['page' => 2]));
        $pageTwo->assertOk()->assertSee('The second excerpt')->assertDontSee('The first excerpt');
    }

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

    /**
     * Regression test: a Project's linked gallery was rendered regardless of
     * its is_active flag (app/CMS/Services/ProjectService.php's cache
     * mapping had no is_active check at all) - unlike every other gallery
     * consumer (gallery_albums/photo_slider/hero sections, the standalone
     * /gallery page), which all go through GalleryService's active() scope.
     */
    public function test_a_project_show_page_does_not_show_an_inactive_linked_gallery(): void
    {
        $gallery = Gallery::factory()->create(['is_active' => false]);
        $gallery->items()->create([
            'type' => 'image',
            'image' => MediaItem::create(['title' => 'P'])->id,
            'caption' => 'Hidden Album Photo',
            'sort_order' => 0,
        ]);

        $project = Project::factory()->create([
            'status' => 'published',
            'slug' => 'gallery-project',
            'gallery_id' => $gallery->id,
        ]);

        $response = $this->get(route('projects.show', $project->slug));

        $response->assertOk()->assertDontSee('Hidden Album Photo');
    }

    /**
     * Regression test: GalleryService::updateAlbum() never called
     * $this->projects->forget(), unlike every other gallery mutator in that
     * file - so an already-cached project's gallery_items kept serving the
     * stale (still-active) snapshot even after the album was switched off.
     */
    public function test_marking_an_already_linked_gallery_inactive_hides_it_from_the_project_page(): void
    {
        $gallery = Gallery::factory()->create(['is_active' => true]);
        $gallery->items()->create([
            'type' => 'image',
            'image' => MediaItem::create(['title' => 'P'])->id,
            'caption' => 'Album Photo',
            'sort_order' => 0,
        ]);

        $project = Project::factory()->create([
            'status' => 'published',
            'slug' => 'toggled-gallery-project',
            'gallery_id' => $gallery->id,
        ]);

        // Warm the cache while the gallery is still active.
        $this->get(route('projects.show', $project->slug))->assertOk()->assertSee('Album Photo');

        app(\App\CMS\Services\GalleryService::class)->updateAlbum($gallery, ['is_active' => false]);

        $response = $this->get(route('projects.show', $project->slug));
        $response->assertOk()->assertDontSee('Album Photo');
    }
}
