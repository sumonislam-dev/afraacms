<?php

namespace App\Http\Controllers\Admin;

use App\CMS\Services\MenuService;
use App\CMS\Services\PageService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMenuRequest;
use App\Http\Requests\Admin\UpdateMenuRequest;
use App\Models\Menu;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MenuController extends Controller
{
    public function __construct(
        private readonly MenuService $menus,
        private readonly PageService $pages,
    ) {
        $this->authorizeResource(Menu::class, 'menu');
    }

    /**
     * Display a listing of the menus.
     */
    public function index(): View
    {
        $menus = Menu::withCount('items')
            ->when(request('search'), fn ($query, $search) => $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")->orWhere('slug', 'like', "%{$search}%");
            }))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.menus.index', compact('menus'));
    }

    /**
     * Show the form for creating a new menu.
     */
    public function create(): View
    {
        return view('admin.menus.create');
    }

    /**
     * Store a newly created menu.
     */
    public function store(StoreMenuRequest $request): RedirectResponse
    {
        $menu = $this->menus->create($request->validated());

        return redirect()->route('admin.menus.edit', $menu)->with('success', __('Menu created successfully.'));
    }

    /**
     * Show the menu builder for the given menu.
     */
    public function edit(Menu $menu): View
    {
        $menu->load(['items' => fn ($query) => $query->orderBy('sort_order')]);

        $homepageId = (int) setting('homepage_page_id');

        $pageOptions = collect($this->pages->all())
            ->map(fn (array $page) => [
                'title' => $page['title'],
                'url' => $page['id'] === $homepageId ? '/' : '/'.$page['slug'],
            ])
            ->all();

        return view('admin.menus.edit', [
            'menu' => $menu,
            'tree' => $menu->tree,
            'pageOptions' => $pageOptions,
        ]);
    }

    /**
     * Update the given menu's name/slug.
     */
    public function update(UpdateMenuRequest $request, Menu $menu): RedirectResponse
    {
        $this->menus->update($menu, $request->validated());

        return redirect()->route('admin.menus.edit', $menu)->with('success', __('Menu updated successfully.'));
    }

    /**
     * Delete the given menu.
     */
    public function destroy(Menu $menu): RedirectResponse
    {
        $this->menus->delete($menu);

        return redirect()->route('admin.menus.index')->with('success', __('Menu deleted successfully.'));
    }
}
