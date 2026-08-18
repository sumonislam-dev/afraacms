<?php

namespace App\CMS\Services;

use App\Models\Page;
use App\Models\Section;
use App\Models\SectionItem;
use Illuminate\Support\Facades\DB;

class SectionService
{
    public function __construct(private readonly PageService $pages) {}

    /**
     * Add a new section to the end of a page.
     */
    public function createSection(Page $page, array $data): Section
    {
        $section = $page->sections()->create([
            ...$data,
            'sort_order' => $page->sections()->count(),
        ]);

        $this->pages->forget();

        return $section;
    }

    /**
     * Update an existing section's own fields.
     */
    public function updateSection(Section $section, array $data): Section
    {
        $section->update($data);

        $this->pages->forget();

        return $section;
    }

    /**
     * Delete a section (and, via cascading foreign keys, its items).
     */
    public function deleteSection(Section $section): void
    {
        $section->delete();

        $this->pages->forget();
    }

    /**
     * Persist a drag-and-drop reordered, flat list of section ids.
     *
     * @param  array<int, int>  $orderedIds
     */
    public function reorderSections(Page $page, array $orderedIds): void
    {
        DB::transaction(function () use ($orderedIds) {
            foreach ($orderedIds as $index => $id) {
                Section::whereKey($id)->update(['sort_order' => $index]);
            }
        });

        $this->pages->forget();
    }

    /**
     * Add a new item to the end of a section's repeatable list.
     */
    public function createItem(Section $section, array $data): SectionItem
    {
        $item = $section->items()->create([
            ...$data,
            'sort_order' => $section->items()->count(),
        ]);

        $this->pages->forget();

        return $item;
    }

    /**
     * Update an existing item's own fields.
     */
    public function updateItem(SectionItem $item, array $data): SectionItem
    {
        $item->update($data);

        $this->pages->forget();

        return $item;
    }

    /**
     * Delete an item.
     */
    public function deleteItem(SectionItem $item): void
    {
        $item->delete();

        $this->pages->forget();
    }
}
