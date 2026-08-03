<?php

namespace App\CMS\Services;

use App\CMS\Services\Concerns\CachesForFrontend;
use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MenuService
{
    use CachesForFrontend;

    protected function cacheKey(): string
    {
        return 'menus.all';
    }

    /**
     * Get a menu by slug, with only its active items nested into a tree, from
     * cache where possible.
     *
     * Returns a plain array, e.g. ['name' => ..., 'slug' => ..., 'tree' => [...]],
     * not a Menu model: the cache store's serializable_classes hardening
     * (see config/cache.php) strips objects down to __PHP_Incomplete_Class
     * on read, so only arrays/scalars may be cached here.
     *
     * @return array{name: string, slug: string, tree: array}|null
     */
    public function render(string $slug): ?array
    {
        return $this->allCached()[$slug] ?? null;
    }

    /**
     * @return array<string, array{name: string, slug: string, tree: array}>
     */
    private function allCached(): array
    {
        return $this->rememberForever(fn () => Menu::query()
            ->with(['items' => fn ($query) => $query->where('is_active', true)])
            ->get()
            ->mapWithKeys(fn (Menu $menu) => [
                $menu->slug => [
                    'name' => $menu->name,
                    'slug' => $menu->slug,
                    'tree' => $this->treeToArray($menu->tree),
                ],
            ])
            ->all());
    }

    /**
     * @param  Collection<int, MenuItem>  $items
     * @return array<int, array>
     */
    private function treeToArray(Collection $items): array
    {
        return $items->map(fn (MenuItem $item) => [
            'label' => $item->label,
            'icon' => $item->icon,
            'type' => $item->type,
            'resolved_url' => $item->resolved_url,
            'target' => $item->target,
            'children' => $this->treeToArray($item->children),
        ])->all();
    }

    /**
     * Create a new, empty menu.
     */
    public function create(array $data): Menu
    {
        $menu = Menu::create($data);

        $this->forget();

        return $menu;
    }

    /**
     * Update a menu's name/slug.
     */
    public function update(Menu $menu, array $data): Menu
    {
        $menu->update($data);

        $this->forget();

        return $menu;
    }

    /**
     * Delete a menu and all of its items.
     */
    public function delete(Menu $menu): void
    {
        $menu->delete();

        $this->forget();
    }

    /**
     * Add a new item to a menu, at the end of its parent's list (top-level
     * if no parent_id is given).
     */
    public function createItem(Menu $menu, array $data): MenuItem
    {
        $parentId = $data['parent_id'] ?? null;

        $item = $menu->items()->create([
            ...$data,
            'sort_order' => $menu->items()->where('parent_id', $parentId)->count(),
        ]);

        $this->forget();

        return $item;
    }

    /**
     * Update an item's own fields (label, type, url, icon, visibility, parent).
     *
     * Moving an item under a different parent appends it to the end of that
     * parent's list, rather than keeping its old sort_order (which could
     * collide with existing siblings there).
     */
    public function updateItem(MenuItem $item, array $data): MenuItem
    {
        $newParentId = $data['parent_id'] ?? null;

        if (array_key_exists('parent_id', $data) && $newParentId != $item->parent_id) {
            $data['sort_order'] = $item->menu->items()->where('parent_id', $newParentId)->count();
        }

        $item->update($data);

        $this->forget();

        return $item;
    }

    /**
     * Delete an item (and, via cascading foreign keys, its descendants).
     */
    public function deleteItem(MenuItem $item): void
    {
        $item->delete();

        $this->forget();
    }

    /**
     * Persist a drag-and-drop reordered/renested tree in one transaction.
     *
     * @param  array<int, array{id: int, children?: array}>  $tree
     */
    public function reorder(Menu $menu, array $tree): void
    {
        DB::transaction(fn () => $this->persistTree($tree, null));

        $this->forget();
    }

    /**
     * @param  array<int, array{id: int, children?: array}>  $nodes
     */
    private function persistTree(array $nodes, ?int $parentId): void
    {
        foreach ($nodes as $index => $node) {
            MenuItem::whereKey($node['id'])->update([
                'parent_id' => $parentId,
                'sort_order' => $index,
            ]);

            $this->persistTree($node['children'] ?? [], (int) $node['id']);
        }
    }
}
