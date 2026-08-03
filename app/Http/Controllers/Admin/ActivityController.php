<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryItem;
use App\Models\MenuItem;
use App\Models\SectionItem;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\View\View;
use Spatie\Activitylog\Models\Activity;

class ActivityController extends Controller
{
    /**
     * Display a paginated, newest-first feed of admin activity across every
     * logged model (Pages, Projects, Banners, Galleries, GalleryItems, Menus,
     * MenuItems, Sections, SectionItems, Users, Roles).
     */
    public function index(): View
    {
        $activities = Activity::query()
            ->with('causer')
            ->with(['subject' => fn (MorphTo $morphTo) => $morphTo->morphWith([
                GalleryItem::class => ['gallery'],
                MenuItem::class => ['menu'],
                SectionItem::class => ['section'],
            ])])
            ->when(request('search'), fn ($query, $search) => $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('subject_type', 'like', "%{$search}%");
            }))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.activity.index', compact('activities'));
    }
}
