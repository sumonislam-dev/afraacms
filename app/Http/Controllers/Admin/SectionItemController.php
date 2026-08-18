<?php

namespace App\Http\Controllers\Admin;

use App\CMS\Services\SectionService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSectionItemRequest;
use App\Http\Requests\Admin\UpdateSectionItemRequest;
use App\Models\Page;
use App\Models\Section;
use App\Models\SectionItem;
use Illuminate\Http\RedirectResponse;

class SectionItemController extends Controller
{
    public function __construct(private readonly SectionService $sections) {}

    /**
     * Add a new item to the end of a section's repeatable list.
     */
    public function store(StoreSectionItemRequest $request, Page $page, Section $section): RedirectResponse
    {
        $this->sections->createItem($section, $request->validated());

        return redirect()
            ->route('admin.pages.sections.edit', [$page, $section])
            ->with('success', __('Item added successfully.'));
    }

    /**
     * Update an existing item.
     */
    public function update(UpdateSectionItemRequest $request, Page $page, Section $section, SectionItem $item): RedirectResponse
    {
        $this->ensureBelongsToSection($section, $item);

        $this->sections->updateItem($item, $request->validated());

        return redirect()
            ->route('admin.pages.sections.edit', [$page, $section])
            ->with('success', __('Item updated successfully.'));
    }

    /**
     * Delete an item.
     */
    public function destroy(Page $page, Section $section, SectionItem $item): RedirectResponse
    {
        $this->authorize('update', $section);
        $this->ensureBelongsToSection($section, $item);

        $this->sections->deleteItem($item);

        return redirect()
            ->route('admin.pages.sections.edit', [$page, $section])
            ->with('success', __('Item deleted successfully.'));
    }

    /**
     * Guard against a section item id from a different section being used here.
     */
    private function ensureBelongsToSection(Section $section, SectionItem $item): void
    {
        abort_if($item->section_id !== $section->id, 404);
    }
}
