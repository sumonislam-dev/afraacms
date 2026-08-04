<?php

namespace App\Http\Controllers;

use App\CMS\Services\GalleryService;
use Illuminate\View\View;

class GalleryController extends Controller
{
    public function __construct(private readonly GalleryService $galleries)
    {
    }

    /**
     * Display the gallery, either as an album list or a single flat grid of
     * every photo, per the "gallery_display_mode" setting.
     */
    public function index(): View
    {
        if (setting('gallery_display_mode', 'albums') === 'flat') {
            return view('frontend.gallery.index', ['items' => $this->galleries->allItemsFlat()]);
        }

        return view('frontend.gallery.index', ['albums' => $this->galleries->all()]);
    }

    /**
     * Display a single album's photos and videos.
     */
    public function show(string $slug): View
    {
        $album = $this->galleries->find($slug);

        abort_unless($album, 404);

        return view('frontend.gallery.show', ['album' => $album]);
    }
}
