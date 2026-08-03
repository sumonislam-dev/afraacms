<?php

namespace Tests\Feature\Admin;

use App\CMS\Services\ProjectService;
use App\Models\Gallery;
use App\Models\MediaItem;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesAdminUsers;
use Tests\TestCase;

class GalleryItemTest extends TestCase
{
    use CreatesAdminUsers, RefreshDatabase;

    public function test_editor_can_add_an_image_item_to_an_album(): void
    {
        $editor = $this->editor();
        $album = Gallery::factory()->create();
        $photo = MediaItem::create(['title' => 'Photo 1']);

        $response = $this->actingAs($editor)->post(route('admin.galleries.items.store', $album), [
            'type' => 'image',
            'image' => $photo->id,
            'caption' => 'A caption',
        ]);

        $response->assertRedirect(route('admin.galleries.edit', $album));
        $this->assertDatabaseHas('gallery_items', [
            'gallery_id' => $album->id,
            'image' => $photo->id,
            'caption' => 'A caption',
        ]);
    }

    public function test_an_image_item_requires_an_image(): void
    {
        $editor = $this->editor();
        $album = Gallery::factory()->create();

        $this->actingAs($editor)
            ->post(route('admin.galleries.items.store', $album), ['type' => 'image'])
            ->assertSessionHasErrors('image');
    }

    public function test_a_user_without_permissions_cannot_add_items(): void
    {
        $user = $this->userWithoutPermissions();
        $album = Gallery::factory()->create();
        $photo = MediaItem::create(['title' => 'Photo 1']);

        $this->actingAs($user)->post(route('admin.galleries.items.store', $album), [
            'type' => 'image',
            'image' => $photo->id,
        ])->assertForbidden();

        $this->assertDatabaseCount('gallery_items', 0);
    }

    public function test_editor_can_bulk_add_photos_with_captions(): void
    {
        $editor = $this->editor();
        $album = Gallery::factory()->create();
        $photoA = MediaItem::create(['title' => 'Photo A']);
        $photoB = MediaItem::create(['title' => 'Photo B']);

        $response = $this->actingAs($editor)->post(route('admin.galleries.items.bulkStore', $album), [
            'photos' => [
                ['id' => $photoA->id, 'caption' => 'First'],
                ['id' => $photoB->id, 'caption' => 'Second'],
            ],
        ]);

        $response->assertRedirect(route('admin.galleries.edit', $album));
        $this->assertDatabaseHas('gallery_items', ['image' => $photoA->id, 'caption' => 'First']);
        $this->assertDatabaseHas('gallery_items', ['image' => $photoB->id, 'caption' => 'Second']);
        $this->assertSame(2, $album->items()->count());
    }

    public function test_editor_can_delete_an_item(): void
    {
        $editor = $this->editor();
        $album = Gallery::factory()->create();
        $item = $album->items()->create(['type' => 'image', 'image' => MediaItem::create(['title' => 'P'])->id, 'sort_order' => 0]);

        $this->actingAs($editor)
            ->delete(route('admin.galleries.items.destroy', [$album, $item]))
            ->assertRedirect(route('admin.galleries.edit', $album));

        $this->assertModelMissing($item);
    }

    public function test_an_item_from_a_different_album_cannot_be_deleted_through_this_album(): void
    {
        $editor = $this->editor();
        $albumA = Gallery::factory()->create();
        $albumB = Gallery::factory()->create();
        $item = $albumB->items()->create(['type' => 'image', 'image' => MediaItem::create(['title' => 'P'])->id, 'sort_order' => 0]);

        $this->actingAs($editor)
            ->delete(route('admin.galleries.items.destroy', [$albumA, $item]))
            ->assertNotFound();

        $this->assertModelExists($item);
    }

    public function test_adding_an_item_busts_the_linked_projects_cache(): void
    {
        $editor = $this->editor();
        $album = Gallery::factory()->create();
        $project = Project::factory()->published()->create(['gallery_id' => $album->id]);

        $projects = app(ProjectService::class);
        $before = $projects->find($project->slug);
        $this->assertSame([], $before['gallery_items']);

        $photo = MediaItem::create(['title' => 'Photo']);
        $this->actingAs($editor)->post(route('admin.galleries.items.store', $album), [
            'type' => 'image',
            'image' => $photo->id,
            'caption' => 'Fresh photo',
        ]);

        $after = $projects->find($project->slug);
        $this->assertCount(1, $after['gallery_items']);
        $this->assertSame('Fresh photo', $after['gallery_items'][0]['caption']);
    }
}
