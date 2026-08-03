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
     * Display every active album.
     */
    public function index(): View
    {
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
