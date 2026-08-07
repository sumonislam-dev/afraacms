<?php

namespace Tests\Feature;

use App\CMS\Services\SettingService;
use App\Models\Gallery;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GalleryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A fresh RefreshDatabase test has no Setting rows at all (no seeder
     * runs automatically), so this must create the row rather than assume
     * it exists - and bust SettingService's own cache afterward, since
     * writing to the model directly bypasses its cache invalidation.
     */
    private function setGalleryDisplayMode(string $mode): void
    {
        Setting::updateOrCreate(['key' => 'gallery_display_mode'], ['value' => $mode, 'group' => 'gallery']);
        app(SettingService::class)->forget();
    }

    public function test_flat_mode_excludes_photos_from_non_public_albums(): void
    {
        $this->setGalleryDisplayMode('flat');

        $public = Gallery::factory()->create(['title' => 'Field Work']);
        $public->items()->create(['type' => 'image', 'caption' => 'Public Photo Caption']);

        $internal = Gallery::factory()->create(['title' => 'Homepage Hero Slides', 'is_public' => false]);
        $internal->items()->create(['type' => 'image', 'caption' => 'Internal Photo Caption']);

        $response = $this->get(route('gallery.index'));

        $response->assertOk()->assertSee('Public Photo Caption')->assertDontSee('Internal Photo Caption');
    }

    public function test_albums_mode_excludes_non_public_albums(): void
    {
        $this->setGalleryDisplayMode('albums');

        Gallery::factory()->create(['title' => 'Field Work']);
        Gallery::factory()->create(['title' => 'Homepage Hero Slides', 'is_public' => false]);

        $response = $this->get(route('gallery.index'));

        $response->assertOk()->assertSee('Field Work')->assertDontSee('Homepage Hero Slides');
    }

    public function test_a_non_public_album_has_no_standalone_page(): void
    {
        $internal = Gallery::factory()->create(['slug' => 'homepage-hero', 'is_public' => false]);

        $this->get(route('gallery.show', $internal->slug))->assertNotFound();
    }

    public function test_a_public_albums_page_still_works(): void
    {
        $public = Gallery::factory()->create(['slug' => 'field-work', 'title' => 'Field Work']);

        $this->get(route('gallery.show', $public->slug))->assertOk()->assertSee('Field Work');
    }
}
