<?php

namespace App\Http\Controllers\Admin;

use App\CMS\Services\GalleryService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReorderGalleriesRequest;
use App\Http\Requests\Admin\StoreGalleryRequest;
use App\Http\Requests\Admin\UpdateGalleryRequest;
use App\Models\Gallery;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class GalleryController extends Controller
{
    public function __construct(private readonly GalleryService $galleries)
    {
        $this->authorizeResource(Gallery::class, 'gallery');
    }

    /**
     * Display the drag-and-drop ordered list of albums.
     */
    public function index(): View
    {
        $albums = Gallery::withCount('items')->orderBy('sort_order')->get();

        return view('admin.galleries.index', compact('albums'));
    }

    /**
     * Show the form for creating a new album.
     */
    public function create(): View
    {
        return view('admin.galleries.create');
    }

    /**
     * Store a newly created album, then continue to its edit screen.
     */
    public function store(StoreGalleryRequest $request): RedirectResponse
    {
        $album = $this->galleries->createAlbum($request->validated());

        return redirect()->route('admin.galleries.edit', $album)->with('success', __('Album created successfully.'));
    }

    /**
     * Show the form for editing the given album (and managing its photos/videos).
     */
    public function edit(Gallery $gallery): View
    {
        $gallery->load('items');

        return view('admin.galleries.edit', ['album' => $gallery]);
    }

    /**
     * Update the given album's own fields.
     */
    public function update(UpdateGalleryRequest $request, Gallery $gallery): RedirectResponse
    {
        $this->galleries->updateAlbum($gallery, $request->validated());

        return redirect()->route('admin.galleries.edit', $gallery)->with('success', __('Album updated successfully.'));
    }

    /**
     * Delete the given album.
     */
    public function destroy(Gallery $gallery): RedirectResponse
    {
        $this->galleries->deleteAlbum($gallery);

        return redirect()->route('admin.galleries.index')->with('success', __('Album deleted successfully.'));
    }

    /**
     * Persist a drag-and-drop reordered list of album ids.
     */
    public function reorder(ReorderGalleriesRequest $request): JsonResponse
    {
        $this->galleries->reorderAlbums($request->validated()['order']);

        return response()->json(['message' => __('Order saved.')]);
    }
}
