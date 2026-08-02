<?php

namespace App\Http\Controllers\Admin;

use App\CMS\Services\MenuService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReorderMenuItemsRequest;
use App\Http\Requests\Admin\StoreMenuItemRequest;
use App\Http\Requests\Admin\UpdateMenuItemRequest;
use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class MenuItemController extends Controller
{
    public function __construct(private readonly MenuService $menus)
    {
    }

    /**
     * Add a new top-level item to the given menu.
     */
    public function store(StoreMenuItemRequest $request, Menu $menu): RedirectResponse
    {
        $this->menus->createItem($menu, $request->validated());

        return redirect()->route('admin.menus.edit', $menu)->with('success', __('Menu item added successfully.'));
    }

    /**
     * Update an existing item's own fields.
     */
    public function update(UpdateMenuItemRequest $request, Menu $menu, MenuItem $menuItem): RedirectResponse
    {
        $this->ensureBelongsToMenu($menu, $menuItem);

        $this->menus->updateItem($menuItem, $request->validated());

        return redirect()->route('admin.menus.edit', $menu)->with('success', __('Menu item updated successfully.'));
    }

    /**
     * Delete an item (and its descendants).
     */
    public function destroy(Menu $menu, MenuItem $menuItem): RedirectResponse
    {
        $this->authorize('update', $menu);
        $this->ensureBelongsToMenu($menu, $menuItem);

        $this->menus->deleteItem($menuItem);

        return redirect()->route('admin.menus.edit', $menu)->with('success', __('Menu item deleted successfully.'));
    }

    /**
     * Persist a drag-and-drop reordered/renested tree.
     */
    public function reorder(ReorderMenuItemsRequest $request, Menu $menu): JsonResponse
    {
        $this->menus->reorder($menu, $request->validated()['tree']);

        return response()->json(['message' => __('Order saved.')]);
    }

    /**
     * Guard against a menu item id from a different menu being used here.
     */
    private function ensureBelongsToMenu(Menu $menu, MenuItem $menuItem): void
    {
        abort_if($menuItem->menu_id !== $menu->id, 404);
    }
}
