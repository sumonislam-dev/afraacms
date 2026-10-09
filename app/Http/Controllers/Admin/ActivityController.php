<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryItem;
use App\Models\MenuItem;
use App\Models\Role;
use App\Models\SectionItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
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
        $viewer = auth()->user();

        $activities = Activity::query()
            ->with('causer')
            ->with(['subject' => fn (MorphTo $morphTo) => $morphTo->morphWith([
                GalleryItem::class => ['gallery'],
                MenuItem::class => ['menu'],
                SectionItem::class => ['section'],
            ])])
            ->when(! $viewer->hasRole('Super Admin'), fn ($query) => $this->excludeSuperAdminActivity($query))
            ->when(request('search'), fn ($query, $search) => $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('subject_type', 'like', "%{$search}%");
            }))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.activity.index', compact('activities'));
    }

    /**
     * Same protection as the Users/Roles list filters - hides log entries
     * about, or performed by, a Super Admin from anyone who isn't one.
     */
    private function excludeSuperAdminActivity(Builder $query): Builder
    {
        $superAdminUserIds = User::role('Super Admin')->pluck('id')->all();
        $superAdminRoleId = Role::where('name', 'Super Admin')->value('id');

        return $query
            ->when($superAdminUserIds !== [], fn ($q) => $q
                ->where(fn ($q2) => $q2->where('causer_type', '!=', User::class)->orWhereNotIn('causer_id', $superAdminUserIds))
                ->where(fn ($q2) => $q2->where('subject_type', '!=', User::class)->orWhereNotIn('subject_id', $superAdminUserIds)))
            ->when($superAdminRoleId, fn ($q) => $q
                ->where(fn ($q2) => $q2->where('subject_type', '!=', Role::class)->orWhere('subject_id', '!=', $superAdminRoleId)));
    }
}
