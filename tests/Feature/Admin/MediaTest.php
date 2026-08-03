<?php

namespace Tests\Feature\Admin;

use App\Models\MediaItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\CreatesAdminUsers;
use Tests\TestCase;

class MediaTest extends TestCase
{
    use CreatesAdminUsers, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('admin.media.index'))->assertRedirect(route('login'));
    }

    public function test_a_user_without_permissions_cannot_view_the_library(): void
    {
        $user = $this->userWithoutPermissions();

        $this->actingAs($user)->get(route('admin.media.index'))->assertForbidden();
    }

    public function test_editor_can_upload_an_image(): void
    {
        $editor = $this->editor();
        $file = UploadedFile::fake()->image('photo.jpg', 800, 600);

        $response = $this->actingAs($editor)->post(route('admin.media.store'), [
            'file' => $file,
            'title' => 'My Photo',
        ]);

        $response->assertRedirect(route('admin.media.index'));
        $this->assertDatabaseHas('media_items', ['title' => 'My Photo']);
    }

    public function test_a_non_image_file_is_rejected(): void
    {
        $editor = $this->editor();
        $file = UploadedFile::fake()->create('document.pdf', 100);

        $this->actingAs($editor)
            ->post(route('admin.media.store'), ['file' => $file])
            ->assertSessionHasErrors('file');
    }

    public function test_a_user_without_permissions_cannot_upload(): void
    {
        $user = $this->userWithoutPermissions();
        $file = UploadedFile::fake()->image('photo.jpg');

        $this->actingAs($user)->post(route('admin.media.store'), ['file' => $file])->assertForbidden();

        $this->assertDatabaseCount('media_items', 0);
    }

    public function test_editor_can_rename_an_item(): void
    {
        $editor = $this->editor();
        $item = MediaItem::create(['title' => 'Old Title']);

        $this->actingAs($editor)->put(route('admin.media.update', $item), [
            'title' => 'New Title',
        ])->assertRedirect(route('admin.media.index'));

        $this->assertSame('New Title', $item->fresh()->title);
    }

    public function test_editor_can_delete_an_item(): void
    {
        $editor = $this->editor();
        $item = MediaItem::create(['title' => 'To Delete']);

        $this->actingAs($editor)
            ->delete(route('admin.media.destroy', $item))
            ->assertRedirect(route('admin.media.index'));

        $this->assertModelMissing($item);
    }

    public function test_a_user_without_permissions_cannot_delete_an_item(): void
    {
        $user = $this->userWithoutPermissions();
        $item = MediaItem::create(['title' => 'Protected']);

        $this->actingAs($user)
            ->delete(route('admin.media.destroy', $item))
            ->assertForbidden();

        $this->assertModelExists($item);
    }

    public function test_search_filters_the_library_by_title(): void
    {
        $editor = $this->editor();
        MediaItem::create(['title' => 'Sunset Beach']);
        MediaItem::create(['title' => 'Mountain View']);

        $this->actingAs($editor)
            ->get(route('admin.media.index', ['search' => 'Sunset']))
            ->assertOk()
            ->assertSee('Sunset Beach')
            ->assertDontSee('Mountain View');
    }
}
