<?php

namespace App\Http\Controllers\Admin;

use App\CMS\Services\SectionService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReorderSectionsRequest;
use App\Http\Requests\Admin\StoreSectionRequest;
use App\Http\Requests\Admin\UpdateSectionRequest;
use App\Models\Gallery;
use App\Models\Page;
use App\Models\Section;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SectionController extends Controller
{
    public function __construct(private readonly SectionService $sections)
    {
        $this->authorizeResource(Section::class, 'section');
    }

    /**
     * Display the ordered list of a page's sections.
     */
    public function index(Page $page): View
    {
        $sections = $page->sections()->get();

        return view('admin.pages.sections.index', compact('page', 'sections'));
    }

    /**
     * Show the form for adding a new section to the page.
     */
    public function create(Page $page): View
    {
        $galleries = Gallery::orderBy('sort_order')->get(['id', 'title', 'is_active']);

        return view('admin.pages.sections.create', compact('page', 'galleries'));
    }

    /**
     * Store a newly created section, then continue to its edit screen.
     */
    public function store(StoreSectionRequest $request, Page $page): RedirectResponse
    {
        $data = $request->validated();
        $galleryIds = $data['galleries'] ?? [];
        unset($data['galleries']);

        $section = $this->sections->createSection($page, $data);
        $section->galleries()->sync($galleryIds);

        return redirect()
            ->route('admin.pages.sections.edit', [$page, $section])
            ->with('success', __('Section added successfully.'));
    }

    /**
     * Show the form for editing the given section (and, for types that
     * support them, managing its repeatable items).
     */
    public function edit(Page $page, Section $section): View
    {
        $section->load(['items', 'galleries']);

        $galleries = Gallery::orderBy('sort_order')->get(['id', 'title', 'is_active']);
        $selectedGalleryIds = $section->galleries->pluck('id')->all();

        return view('admin.pages.sections.edit', compact('page', 'section', 'galleries', 'selectedGalleryIds'));
    }

    /**
     * Update the given section's own fields.
     */
    public function update(UpdateSectionRequest $request, Page $page, Section $section): RedirectResponse
    {
        $data = $request->validated();
        $galleryIds = $data['galleries'] ?? [];
        unset($data['galleries']);

        $this->sections->updateSection($section, $data);
        $section->galleries()->sync($galleryIds);

        return redirect()
            ->route('admin.pages.sections.edit', [$page, $section])
            ->with('success', __('Section updated successfully.'));
    }

    /**
     * Delete the given section.
     */
    public function destroy(Page $page, Section $section): RedirectResponse
    {
        $this->sections->deleteSection($section);

        return redirect()->route('admin.pages.sections.index', $page)->with('success', __('Section deleted successfully.'));
    }

    /**
     * Persist a drag-and-drop reordered list of section ids.
     */
    public function reorder(ReorderSectionsRequest $request, Page $page): JsonResponse
    {
        $this->sections->reorderSections($page, $request->validated()['order']);

        return response()->json(['message' => __('Order saved.')]);
    }
}
