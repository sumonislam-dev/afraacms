<?php

namespace Tests\Feature\Admin;

use App\Models\Gallery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesAdminUsers;
use Tests\TestCase;

class GalleryTest extends TestCase
{
    use CreatesAdminUsers, RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('admin.galleries.index'))->assertRedirect(route('login'));
    }

    public function test_a_user_without_permissions_cannot_view_galleries(): void
    {
        $user = $this->userWithoutPermissions();

        $this->actingAs($user)->get(route('admin.galleries.index'))->assertForbidden();
    }

    public function test_editor_can_view_the_album_list(): void
    {
        $editor = $this->editor();
        Gallery::factory()->create(['title' => 'Summer Fair']);

        $this->actingAs($editor)
            ->get(route('admin.galleries.index'))
            ->assertOk()
            ->assertSee('Summer Fair');
    }

    public function test_editor_can_create_an_album(): void
    {
        $editor = $this->editor();

        $response = $this->actingAs($editor)->post(route('admin.galleries.store'), [
            'title' => 'Winter Gala',
            'slug' => 'winter-gala',
        ]);

        $gallery = Gallery::where('slug', 'winter-gala')->firstOrFail();
        $response->assertRedirect(route('admin.galleries.edit', $gallery));
        $this->assertDatabaseHas('galleries', ['slug' => 'winter-gala']);
    }

    public function test_an_album_slug_must_be_unique(): void
    {
        $editor = $this->editor();
        Gallery::factory()->create(['slug' => 'existing-album']);

        $this->actingAs($editor)
            ->post(route('admin.galleries.store'), ['title' => 'Duplicate', 'slug' => 'existing-album'])
            ->assertSessionHasErrors('slug');
    }

    public function test_a_user_without_permissions_cannot_create_an_album(): void
    {
        $user = $this->userWithoutPermissions();

        $this->actingAs($user)->post(route('admin.galleries.store'), [
            'title' => 'Sneaky Album',
            'slug' => 'sneaky-album',
        ])->assertForbidden();

        $this->assertDatabaseMissing('galleries', ['slug' => 'sneaky-album']);
    }

    public function test_editor_can_update_an_album(): void
    {
        $editor = $this->editor();
        $album = Gallery::factory()->create(['title' => 'Old Title']);

        $this->actingAs($editor)->put(route('admin.galleries.update', $album), [
            'title' => 'New Title',
            'slug' => $album->slug,
            'is_active' => false,
        ])->assertRedirect(route('admin.galleries.edit', $album));

        $album->refresh();
        $this->assertSame('New Title', $album->title);
        $this->assertFalse($album->is_active);
    }

    public function test_editor_can_turn_off_public_gallery_visibility(): void
    {
        $editor = $this->editor();
        $album = Gallery::factory()->create(['title' => 'Homepage Hero Slides']);

        $this->actingAs($editor)->put(route('admin.galleries.update', $album), [
            'title' => $album->title,
            'slug' => $album->slug,
            'is_public' => false,
        ])->assertRedirect(route('admin.galleries.edit', $album));

        $this->assertFalse($album->fresh()->is_public);
    }

    public function test_editor_can_delete_an_album(): void
    {
        $editor = $this->editor();
        $album = Gallery::factory()->create();

        $this->actingAs($editor)
            ->delete(route('admin.galleries.destroy', $album))
            ->assertRedirect(route('admin.galleries.index'));

        $this->assertModelMissing($album);
    }

    public function test_a_user_without_permissions_cannot_delete_an_album(): void
    {
        $user = $this->userWithoutPermissions();
        $album = Gallery::factory()->create();

        $this->actingAs($user)
            ->delete(route('admin.galleries.destroy', $album))
            ->assertForbidden();

        $this->assertModelExists($album);
    }
}
