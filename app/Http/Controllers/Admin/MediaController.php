<?php

namespace App\Http\Controllers\Admin;

use App\CMS\Services\MediaService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReplaceMediaRequest;
use App\Http\Requests\Admin\StoreMediaRequest;
use App\Http\Requests\Admin\UpdateMediaRequest;
use App\Models\MediaItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MediaController extends Controller
{
    public function __construct(private readonly MediaService $media)
    {
        $this->authorizeResource(MediaItem::class, 'mediaItem');
    }

    /**
     * List/search the media library.
     *
     * Serves both the full admin page and the media picker's AJAX search,
     * since the picker just requests the same URL with an "Accept: application/json"
     * header instead of loading a whole second endpoint.
     */
    public function index(Request $request): View|JsonResponse
    {
        $items = $this->media->search($request->string('search')->toString() ?: null);

        if ($request->wantsJson()) {
            return response()->json([
                'items' => $items->through(fn (MediaItem $item) => [
                    'id' => $item->id,
                    'title' => $item->title,
                    'thumb_url' => $item->thumb_url,
                    'file_url' => $item->file_url,
                ]),
            ]);
        }

        return view('admin.media.index', ['items' => $items]);
    }

    /**
     * Upload one or more new files into the library.
     */
    public function store(StoreMediaRequest $request): RedirectResponse|JsonResponse
    {
        $item = $this->media->upload($request->file('file'), $request->user(), $request->input('title'));

        if ($request->wantsJson()) {
            return response()->json([
                'item' => [
                    'id' => $item->id,
                    'title' => $item->title,
                    'thumb_url' => $item->thumb_url,
                    'file_url' => $item->file_url,
                ],
            ], 201);
        }

        return redirect()->route('admin.media.index')->with('success', __('File uploaded successfully.'));
    }

    /**
     * Rename an existing item.
     */
    public function update(UpdateMediaRequest $request, MediaItem $mediaItem): RedirectResponse
    {
        $this->media->rename($mediaItem, $request->string('title')->toString());

        return redirect()->route('admin.media.index')->with('success', __('Renamed successfully.'));
    }

    /**
     * Replace an existing item's file.
     */
    public function replace(ReplaceMediaRequest $request, MediaItem $mediaItem): RedirectResponse
    {
        $this->media->replace($mediaItem, $request->file('file'));

        return redirect()->route('admin.media.index')->with('success', __('File replaced successfully.'));
    }

    /**
     * Delete an item.
     */
    public function destroy(MediaItem $mediaItem): RedirectResponse
    {
        $this->media->delete($mediaItem);

        return redirect()->route('admin.media.index')->with('success', __('Deleted successfully.'));
    }
}
