<?php

namespace Tests\Feature;

use App\Models\MediaItem;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageBannerOverrideTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_page_with_its_own_banner_eyebrow_shows_it_instead_of_the_shared_banner(): void
    {
        $page = Page::factory()->create([
            'slug' => 'about',
            'status' => 'published',
            'banner_eyebrow' => 'About RSUF',
        ]);

        $this->get('/'.$page->slug)
            ->assertOk()
            ->assertSee('About RSUF');
    }

    public function test_a_page_without_a_banner_override_renders_without_error(): void
    {
        $page = Page::factory()->create([
            'slug' => 'get-involved',
            'status' => 'published',
        ]);

        $this->get('/'.$page->slug)->assertOk();
    }

    public function test_a_pages_banner_image_override_renders(): void
    {
        $photo = MediaItem::create(['title' => 'Campus']);
        $page = Page::factory()->create([
            'slug' => 'about',
            'status' => 'published',
            'banner_image' => $photo->id,
        ]);

        $this->get('/'.$page->slug)->assertOk();
    }
}
