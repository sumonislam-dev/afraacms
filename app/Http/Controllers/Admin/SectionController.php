<?php

namespace App\Http\Controllers\Admin;

use App\CMS\Services\SectionService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReorderSectionsRequest;
use App\Http\Requests\Admin\StoreSectionRequest;
use App\Http\Requests\Admin\UpdateSectionRequest;
use App\Models\AnnualReport;
use App\Models\Gallery;
use App\Models\NewsCategory;
use App\Models\NewsPost;
use App\Models\Page;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Section;
use App\Models\Story;
use App\Models\StoryCategory;
use App\Models\TeamCategory;
use App\Models\TeamMember;
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
        $teamMembers = TeamMember::where('is_active', true)->orderBy('sort_order')->get(['id', 'name', 'role', 'category_id']);
        $teamCategories = TeamCategory::orderBy('name')->get(['id', 'name']);

        return view('admin.pages.sections.create', array_merge(
            compact('page', 'galleries', 'teamMembers', 'teamCategories'),
            $this->contentSourceOptions()
        ));
    }

    /**
     * Store a newly created section, then continue to its edit screen.
     */
    public function store(StoreSectionRequest $request, Page $page): RedirectResponse
    {
        $data = $request->validated();
        $galleryIds = $data['galleries'] ?? [];
        $teamMemberIds = $data['team_members'] ?? [];
        $teamCategoryIds = $data['team_category_ids'] ?? [];
        unset($data['galleries'], $data['team_members'], $data['team_category_ids']);

        $contentCategoryIds = $data['content_category_ids'] ?? [];
        $contentItemIds = $data['content_item_ids'] ?? [];
        unset($data['content_category_ids'], $data['content_item_ids']);

        $section = $this->sections->createSection($page, $data);
        $section->galleries()->sync($galleryIds);
        $section->teamMembers()->sync($teamMemberIds);
        $section->teamCategories()->sync($teamCategoryIds);
        $this->syncContentListPivots($section, $data, $contentCategoryIds, $contentItemIds);

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
        $section->load([
            'items', 'galleries', 'teamMembers', 'teamCategories', 'newsPosts', 'newsCategories',
            'projectCategories', 'projectItems', 'storyItems', 'storyCategories', 'annualReportItems',
        ]);

        $galleries = Gallery::orderBy('sort_order')->get(['id', 'title', 'is_active']);
        $selectedGalleryIds = $section->galleries->pluck('id')->all();

        $teamMembers = TeamMember::where('is_active', true)->orderBy('sort_order')->get(['id', 'name', 'role', 'category_id']);
        $teamCategories = TeamCategory::orderBy('name')->get(['id', 'name']);
        $selectedTeamMemberIds = $section->teamMembers->pluck('id')->all();
        $selectedTeamCategoryIds = $section->teamCategories->pluck('id')->all();

        $selectedContentCategoryIds = match ($section->source) {
            'news', 'notices' => $section->newsCategories->pluck('id')->all(),
            'projects' => $section->projectCategories->pluck('id')->all(),
            'stories' => $section->storyCategories->pluck('id')->all(),
            default => [],
        };
        $selectedContentItemIds = match ($section->source) {
            'news', 'notices' => $section->newsPosts->pluck('id')->all(),
            'projects' => $section->projectItems->pluck('id')->all(),
            'stories' => $section->storyItems->pluck('id')->all(),
            'annual_reports' => $section->annualReportItems->pluck('id')->all(),
            default => [],
        };

        return view('admin.pages.sections.edit', array_merge(compact(
            'page', 'section', 'galleries', 'selectedGalleryIds',
            'teamMembers', 'teamCategories', 'selectedTeamMemberIds', 'selectedTeamCategoryIds',
            'selectedContentCategoryIds', 'selectedContentItemIds'
        ), $this->contentSourceOptions()));
    }

    /**
     * Update the given section's own fields.
     */
    public function update(UpdateSectionRequest $request, Page $page, Section $section): RedirectResponse
    {
        $data = $request->validated();
        $galleryIds = $data['galleries'] ?? [];
        $teamMemberIds = $data['team_members'] ?? [];
        $teamCategoryIds = $data['team_category_ids'] ?? [];
        unset($data['galleries'], $data['team_members'], $data['team_category_ids']);

        $contentCategoryIds = $data['content_category_ids'] ?? [];
        $contentItemIds = $data['content_item_ids'] ?? [];
        unset($data['content_category_ids'], $data['content_item_ids']);

        $this->sections->updateSection($section, $data);
        $section->galleries()->sync($galleryIds);
        $section->teamMembers()->sync($teamMemberIds);
        $section->teamCategories()->sync($teamCategoryIds);
        $this->syncContentListPivots($section, $data, $contentCategoryIds, $contentItemIds);

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

    /**
     * Category/item pickers for every content_sources source, keyed by
     * source key, for the "content_list" (global) Section type's form.
     *
     * @return array{contentCategoryOptions: array<string, \Illuminate\Support\Collection>, contentItemOptions: array<string, \Illuminate\Support\Collection>}
     */
    private function contentSourceOptions(): array
    {
        return [
            'contentCategoryOptions' => [
                'news' => NewsCategory::orderBy('name')->get(['id', 'name']),
                'notices' => NewsCategory::orderBy('name')->get(['id', 'name']),
                'stories' => StoryCategory::orderBy('name')->get(['id', 'name']),
                'projects' => ProjectCategory::orderBy('name')->get(['id', 'name']),
            ],
            'contentItemOptions' => [
                'news' => NewsPost::orderByDesc('published_at')->get(['id', 'title', 'category_id']),
                'notices' => NewsPost::whereHas('media', fn ($q) => $q->where('collection_name', 'attachment'))->orderByDesc('published_at')->get(['id', 'title', 'category_id']),
                'stories' => Story::orderByDesc('published_at')->get(['id', 'title']),
                'projects' => Project::orderBy('title')->get(['id', 'title', 'category_id']),
                'annual_reports' => AnnualReport::orderByDesc('year')->get(['id', 'title']),
            ],
        ];
    }

    /**
     * For the "content_list" (global) Section type only: sync whichever
     * category/item pivots match the submitted source, and clear the ones
     * for every other source (so switching source on an existing section
     * doesn't leave stale picks behind).
     */
    private function syncContentListPivots(Section $section, array $data, array $contentCategoryIds, array $contentItemIds): void
    {
        if (($data['type'] ?? null) !== 'content_list') {
            return;
        }

        $source = $data['source'] ?? null;
        $categoriesRelation = $source ? config("content_sources.{$source}.categories_relation") : null;
        $itemsRelation = $source ? config("content_sources.{$source}.items_relation") : null;

        foreach (['newsCategories', 'projectCategories', 'storyCategories'] as $relation) {
            $section->$relation()->sync($relation === $categoriesRelation ? $contentCategoryIds : []);
        }

        foreach (['newsPosts', 'projectItems', 'storyItems', 'annualReportItems'] as $relation) {
            $section->$relation()->sync($relation === $itemsRelation ? $contentItemIds : []);
        }
    }
}
