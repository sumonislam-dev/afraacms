<?php

namespace App\Http\Controllers\Admin;

use App\CMS\Services\GalleryService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BulkStoreGalleryItemsRequest;
use App\Http\Requests\Admin\ReorderGalleryItemsRequest;
use App\Http\Requests\Admin\StoreGalleryItemRequest;
use App\Http\Requests\Admin\UpdateGalleryItemRequest;
use App\Models\Gallery;
use App\Models\GalleryItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class GalleryItemController extends Controller
{
    public function __construct(private readonly GalleryService $galleries)
    {
    }

    /**
     * Add a new photo/video to the end of an album's repeatable list.
     */
    public function store(StoreGalleryItemRequest $request, Gallery $gallery): RedirectResponse
    {
        $this->galleries->createItem($gallery, $request->validated());

        return redirect()
            ->route('admin.galleries.edit', $gallery)
            ->with('success', __('Item added successfully.'));
    }

    /**
     * Add several photos at once, each becoming its own item (with its own
     * optional caption) at the end of the album's repeatable list, in the
     * order they were picked.
     */
    public function bulkStore(BulkStoreGalleryItemsRequest $request, Gallery $gallery): RedirectResponse
    {
        $photos = $request->validated('photos');

        foreach ($photos as $photo) {
            $this->galleries->createItem($gallery, [
                'type' => 'image',
                'image' => $photo['id'],
                'caption' => $photo['caption'] ?? null,
            ]);
        }

        return redirect()
            ->route('admin.galleries.edit', $gallery)
            ->with('success', trans_choice(':count photo added successfully.|:count photos added successfully.', count($photos), ['count' => count($photos)]));
    }

    /**
     * Update an existing item.
     */
    public function update(UpdateGalleryItemRequest $request, Gallery $gallery, GalleryItem $item): RedirectResponse
    {
        $this->ensureBelongsToGallery($gallery, $item);

        $this->galleries->updateItem($item, $request->validated());

        return redirect()
            ->route('admin.galleries.edit', $gallery)
            ->with('success', __('Item updated successfully.'));
    }

    /**
     * Delete an item.
     */
    public function destroy(Gallery $gallery, GalleryItem $item): RedirectResponse
    {
        $this->authorize('update', $gallery);
        $this->ensureBelongsToGallery($gallery, $item);

        $this->galleries->deleteItem($item);

        return redirect()
            ->route('admin.galleries.edit', $gallery)
            ->with('success', __('Item deleted successfully.'));
    }

    /**
     * Persist a drag-and-drop reordered list of item ids within an album.
     */
    public function reorder(ReorderGalleryItemsRequest $request, Gallery $gallery): JsonResponse
    {
        $this->galleries->reorderItems($gallery, $request->validated()['order']);

        return response()->json(['message' => __('Order saved.')]);
    }

    /**
     * Guard against a gallery item id from a different album being used here.
     */
    private function ensureBelongsToGallery(Gallery $gallery, GalleryItem $item): void
    {
        abort_if($item->gallery_id !== $gallery->id, 404);
    }
}
