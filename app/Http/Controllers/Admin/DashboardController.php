<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Certificate;
use App\Models\ContactMessage;
use App\Models\Donation;
use App\Models\Enrollment;
use App\Models\Gallery;
use App\Models\GalleryItem;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\NewsPost;
use App\Models\Page;
use App\Models\Project;
use App\Models\Role;
use App\Models\SectionItem;
use App\Models\User;
use App\Models\VisitorBookEntry;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\View\View;
use Spatie\Activitylog\Models\Activity;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard: permission-gated stat cards, quick
     * actions, and a recent-activity feed.
     */
    public function index(): View
    {
        $user = auth()->user();

        $cards = collect([
            [
                'permission' => 'pages.view',
                'label' => __('Pages'),
                'value' => Page::count(),
                'sub' => __(':n published', ['n' => Page::published()->count()]),
                'icon' => 'document-text',
                'route' => 'admin.pages.index',
            ],
            [
                'permission' => 'projects.view',
                'label' => __('Projects'),
                'value' => Project::count(),
                'sub' => __(':n published', ['n' => Project::published()->count()]),
                'icon' => 'chart-bar',
                'route' => 'admin.projects.index',
            ],
            [
                'permission' => 'gallery.view',
                'label' => __('Galleries'),
                'value' => Gallery::count(),
                'sub' => __(':n active', ['n' => Gallery::where('is_active', true)->count()]),
                'icon' => 'photo',
                'route' => 'admin.galleries.index',
            ],
            [
                'permission' => 'banners.view',
                'label' => __('Banners'),
                'value' => Banner::count(),
                'sub' => __(':n active', ['n' => Banner::where('is_active', true)->count()]),
                'icon' => 'bars-4',
                'route' => 'admin.banners.index',
            ],
            [
                'permission' => 'menus.view',
                'label' => __('Menus'),
                'value' => Menu::count(),
                'sub' => __(':n items', ['n' => MenuItem::count()]),
                'icon' => 'bars-3',
                'route' => 'admin.menus.index',
            ],
            [
                'permission' => 'contact.view',
                'label' => __('Contact Messages'),
                'value' => ContactMessage::count(),
                'sub' => __(':n unread', ['n' => ContactMessage::unread()->count()]),
                'icon' => 'phone',
                'route' => 'admin.contact.index',
                'alert' => ContactMessage::unread()->exists(),
            ],
            [
                'permission' => 'donations.view',
                'label' => __('Donations'),
                'value' => Donation::count(),
                'sub' => __(':n completed', ['n' => Donation::completed()->count()]),
                'icon' => 'currency-dollar',
                'route' => 'admin.donations.index',
            ],
            [
                'permission' => 'visitor_book.view',
                'label' => __('Visitor Book'),
                'value' => VisitorBookEntry::count(),
                'sub' => __(':n pending approval', ['n' => VisitorBookEntry::pending()->count()]),
                'icon' => 'pencil',
                'route' => 'admin.visitor-book.index',
                'alert' => VisitorBookEntry::pending()->exists(),
            ],
            [
                'permission' => 'enrollments.view',
                'label' => __('Enrollments'),
                'value' => Enrollment::count(),
                'sub' => __(':n certificates issued', ['n' => Enrollment::certificateValid()->count()]),
                'icon' => 'check-circle',
                'route' => 'admin.enrollments.index',
            ],
            [
                'permission' => 'certificates.view',
                'label' => __('Certificates'),
                'value' => Certificate::count(),
                'sub' => __(':n valid', ['n' => Certificate::valid()->count()]),
                'icon' => 'shield-check',
                'route' => 'admin.certificates.index',
            ],
            [
                'permission' => 'news.view',
                'label' => __('News'),
                'value' => NewsPost::count(),
                'sub' => __(':n published', ['n' => NewsPost::published()->count()]),
                'icon' => 'bell',
                'route' => 'admin.news.index',
            ],
            [
                'permission' => 'users.view',
                'label' => __('Users'),
                'value' => User::count(),
                'icon' => 'users',
                'route' => 'admin.users.index',
            ],
            [
                'permission' => 'roles.view',
                'label' => __('Roles'),
                'value' => Role::count(),
                'icon' => 'user-circle',
                'route' => 'admin.roles.index',
            ],
        ])->filter(fn (array $card) => $user->can($card['permission']))->values();

        $quickActions = collect([
            ['permission' => 'pages.create', 'label' => __('New Page'), 'route' => 'admin.pages.create', 'icon' => 'document-text'],
            ['permission' => 'projects.create', 'label' => __('New Project'), 'route' => 'admin.projects.create', 'icon' => 'chart-bar'],
            ['permission' => 'gallery.create', 'label' => __('New Gallery'), 'route' => 'admin.galleries.create', 'icon' => 'photo'],
            ['permission' => 'banners.create', 'label' => __('New Banner'), 'route' => 'admin.banners.create', 'icon' => 'bars-4'],
        ])->filter(fn (array $action) => $user->can($action['permission']))->values();

        $recentActivity = $user->can('activity.view')
            ? Activity::query()
                ->with('causer')
                ->with(['subject' => fn (MorphTo $morphTo) => $morphTo->morphWith([
                    GalleryItem::class => ['gallery'],
                    MenuItem::class => ['menu'],
                    SectionItem::class => ['section'],
                ])])
                ->latest()
                ->limit(8)
                ->get()
            : collect();

        return view('admin.dashboard', compact('cards', 'quickActions', 'recentActivity'));
    }
}
