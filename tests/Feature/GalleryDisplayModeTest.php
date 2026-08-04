<?php

namespace Tests\Feature;

use App\Models\Gallery;
use App\Models\MediaItem;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GalleryDisplayModeTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_public_gallery_shows_albums_by_default(): void
    {
        Gallery::factory()->create(['title' => 'Summer Fair']);

        $this->get(route('gallery.index'))
            ->assertOk()
            ->assertSee('Summer Fair');
    }

    public function test_the_public_gallery_shows_a_flat_grid_when_configured(): void
    {
        Setting::create(['group' => 'gallery', 'key' => 'gallery_display_mode', 'value' => 'flat']);

        $album = Gallery::factory()->create(['title' => 'Field Work']);
        $photo = MediaItem::create(['title' => 'Photo 1']);
        $album->items()->create(['type' => 'image', 'image' => $photo->id, 'caption' => 'A caption', 'sort_order' => 0]);

        $response = $this->get(route('gallery.index'));

        $response->assertOk();
        $response->assertDontSee('Field Work');
        $response->assertSee('A caption');
    }

    public function test_the_flat_gallery_shows_no_photos_message_when_empty(): void
    {
        Setting::create(['group' => 'gallery', 'key' => 'gallery_display_mode', 'value' => 'flat']);

        $this->get(route('gallery.index'))
            ->assertOk()
            ->assertSee('No photos yet');
    }
}
