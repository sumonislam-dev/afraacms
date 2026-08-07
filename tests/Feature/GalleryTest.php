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

    private function setGalleryShowCaptions(bool $show): void
    {
        Setting::updateOrCreate(['key' => 'gallery_show_captions'], ['value' => $show ? '1' : '0', 'group' => 'gallery']);
        app(SettingService::class)->forget();
    }

    private function setGalleryItemsPerPage(int $perPage): void
    {
        Setting::updateOrCreate(['key' => 'gallery_items_per_page'], ['value' => (string) $perPage, 'group' => 'gallery']);
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

    public function test_captions_show_by_default_in_flat_mode(): void
    {
        $this->setGalleryDisplayMode('flat');

        $album = Gallery::factory()->create(['title' => 'Field Work']);
        $album->items()->create(['type' => 'image', 'caption' => 'A Caption']);

        $this->get(route('gallery.index'))->assertOk()->assertSee('A Caption');
    }

    public function test_captions_are_hidden_when_the_setting_is_off_in_flat_mode(): void
    {
        $this->setGalleryDisplayMode('flat');
        $this->setGalleryShowCaptions(false);

        $album = Gallery::factory()->create(['title' => 'Field Work']);
        $album->items()->create(['type' => 'image', 'caption' => 'A Caption']);

        $this->get(route('gallery.index'))->assertOk()->assertDontSee('A Caption');
    }

    public function test_captions_are_hidden_on_an_album_page_when_the_setting_is_off(): void
    {
        $this->setGalleryShowCaptions(false);

        $album = Gallery::factory()->create(['slug' => 'field-work', 'title' => 'Field Work']);
        $album->items()->create(['type' => 'image', 'caption' => 'A Caption']);

        $this->get(route('gallery.show', $album->slug))->assertOk()->assertDontSee('A Caption');
    }

    public function test_the_flat_grid_paginates_per_the_items_per_page_setting(): void
    {
        $this->setGalleryDisplayMode('flat');
        $this->setGalleryItemsPerPage(2);

        $album = Gallery::factory()->create(['title' => 'Field Work']);
        $album->items()->create(['type' => 'image', 'caption' => 'Photo One']);
        $album->items()->create(['type' => 'image', 'caption' => 'Photo Two']);
        $album->items()->create(['type' => 'image', 'caption' => 'Photo Three']);

        $pageOne = $this->get(route('gallery.index'));
        $pageOne->assertOk()->assertSee('Photo One')->assertSee('Photo Two')->assertDontSee('Photo Three');
        $pageOne->assertSee('1 of 2');

        $pageTwo = $this->get(route('gallery.index', ['page' => 2]));
        $pageTwo->assertOk()->assertSee('Photo Three')->assertDontSee('Photo One')->assertDontSee('Photo Two');
    }

    public function test_an_album_page_paginates_per_the_items_per_page_setting(): void
    {
        $this->setGalleryItemsPerPage(2);

        $album = Gallery::factory()->create(['slug' => 'field-work', 'title' => 'Field Work']);
        $album->items()->create(['type' => 'image', 'caption' => 'Photo One']);
        $album->items()->create(['type' => 'image', 'caption' => 'Photo Two']);
        $album->items()->create(['type' => 'image', 'caption' => 'Photo Three']);

        $pageOne = $this->get(route('gallery.show', $album->slug));
        $pageOne->assertOk()->assertSee('Photo One')->assertSee('Photo Two')->assertDontSee('Photo Three');

        $pageTwo = $this->get(route('gallery.show', $album->slug).'?page=2');
        $pageTwo->assertOk()->assertSee('Photo Three')->assertDontSee('Photo One');
    }
}
