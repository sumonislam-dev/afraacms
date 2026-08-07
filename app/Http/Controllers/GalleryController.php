<?php

namespace App\Http\Controllers;

use App\CMS\Services\GalleryService;
use Illuminate\Pagination\LengthAwarePaginator;
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
            $paginator = $this->paginate($this->galleries->allItemsFlat());

            return view('frontend.gallery.index', [
                'items' => $paginator->items(),
                'paginator' => $paginator,
            ]);
        }

        return view('frontend.gallery.index', ['albums' => $this->galleries->allPublic()]);
    }

    /**
     * Display a single album's photos and videos.
     */
    public function show(string $slug): View
    {
        $album = $this->galleries->find($slug);

        abort_unless($album, 404);

        $paginator = $this->paginate($album['items']);
        $album['items'] = $paginator->items();

        return view('frontend.gallery.show', ['album' => $album, 'paginator' => $paginator]);
    }

    /**
     * Slice a flat items array to the current page, per the
     * "gallery_items_per_page" setting.
     *
     * @param  array<int, array>  $items
     */
    private function paginate(array $items): LengthAwarePaginator
    {
        $perPage = max(1, (int) setting('gallery_items_per_page', 24));
        $page = LengthAwarePaginator::resolveCurrentPage();

        return new LengthAwarePaginator(
            array_slice($items, ($page - 1) * $perPage, $perPage),
            count($items),
            $perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );
    }
}
