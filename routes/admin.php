<?php

use App\Http\Controllers\Admin\ActivityController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\CertificateController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\DonationController;
use App\Http\Controllers\Admin\EnrollmentController;
use App\Http\Controllers\Admin\FeaturedVisitorController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\GalleryItemController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\MenuItemController;
use App\Http\Controllers\Admin\NewsCategoryController;
use App\Http\Controllers\Admin\NewsPostController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ProjectCategoryController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SectionController;
use App\Http\Controllers\Admin\SectionItemController;
use App\Http\Controllers\Admin\SeoController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\StoryController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\TeamCategoryController;
use App\Http\Controllers\Admin\TeamMemberController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VisitorBookEntryController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])
            ->middleware('permission:dashboard.view')
            ->name('dashboard');

        Route::resource('users', UserController::class)->except('show');
        Route::patch('users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');

        Route::resource('roles', RoleController::class)->except('show');

        Route::get('permissions', [PermissionController::class, 'index'])->name('permissions.index');

        Route::get('activity', [ActivityController::class, 'index'])->middleware('permission:activity.view')->name('activity.index');

        Route::get('settings', [SettingController::class, 'edit'])->name('settings.edit');
        Route::put('settings', [SettingController::class, 'update'])->name('settings.update');

        Route::get('seo', [SeoController::class, 'edit'])->middleware('permission:seo.view')->name('seo.edit');
        Route::put('seo', [SeoController::class, 'update'])->middleware('permission:seo.edit')->name('seo.update');

        Route::get('media', [MediaController::class, 'index'])->name('media.index');
        Route::post('media', [MediaController::class, 'store'])->name('media.store');
        Route::put('media/{mediaItem}', [MediaController::class, 'update'])->name('media.update');
        Route::post('media/{mediaItem}/replace', [MediaController::class, 'replace'])->name('media.replace');
        Route::delete('media/{mediaItem}', [MediaController::class, 'destroy'])->name('media.destroy');

        Route::resource('menus', MenuController::class)->except('show');
        Route::post('menus/{menu}/items', [MenuItemController::class, 'store'])->name('menus.items.store');
        Route::put('menus/{menu}/items/{menuItem}', [MenuItemController::class, 'update'])->name('menus.items.update');
        Route::delete('menus/{menu}/items/{menuItem}', [MenuItemController::class, 'destroy'])->name('menus.items.destroy');
        Route::post('menus/{menu}/items/reorder', [MenuItemController::class, 'reorder'])->name('menus.items.reorder');

        Route::resource('pages', PageController::class)->except('show');
        Route::get('pages-trash', [PageController::class, 'trash'])->name('pages.trash');
        Route::post('pages/{page}/restore', [PageController::class, 'restore'])->name('pages.restore')->withTrashed();
        Route::delete('pages/{page}/force', [PageController::class, 'forceDelete'])->name('pages.force-delete')->withTrashed();

        Route::resource('banners', BannerController::class)->except('show');

        Route::resource('galleries', GalleryController::class)->except('show');
        Route::post('galleries/reorder', [GalleryController::class, 'reorder'])->name('galleries.reorder');

        Route::prefix('galleries/{gallery}')->name('galleries.')->group(function () {
            Route::post('items', [GalleryItemController::class, 'store'])->name('items.store');
            Route::post('items/bulk', [GalleryItemController::class, 'bulkStore'])->name('items.bulkStore');
            Route::put('items/{item}', [GalleryItemController::class, 'update'])->name('items.update');
            Route::delete('items/{item}', [GalleryItemController::class, 'destroy'])->name('items.destroy');
            Route::post('items/reorder', [GalleryItemController::class, 'reorder'])->name('items.reorder');
        });

        Route::resource('project-categories', ProjectCategoryController::class)
            ->except(['show', 'create', 'edit'])
            ->parameters(['project-categories' => 'category']);

        Route::resource('projects', ProjectController::class)->except('show');
        Route::get('projects-trash', [ProjectController::class, 'trash'])->name('projects.trash');
        Route::post('projects/{project}/restore', [ProjectController::class, 'restore'])->name('projects.restore')->withTrashed();
        Route::delete('projects/{project}/force', [ProjectController::class, 'forceDelete'])->name('projects.force-delete')->withTrashed();

        Route::resource('team-categories', TeamCategoryController::class)
            ->except(['show', 'create', 'edit'])
            ->parameters(['team-categories' => 'category']);

        Route::resource('team', TeamMemberController::class)->except('show');
        Route::get('team-trash', [TeamMemberController::class, 'trash'])->name('team.trash');
        Route::post('team/{team}/restore', [TeamMemberController::class, 'restore'])->name('team.restore')->withTrashed();
        Route::delete('team/{team}/force', [TeamMemberController::class, 'forceDelete'])->name('team.force-delete')->withTrashed();

        Route::resource('news-categories', NewsCategoryController::class)
            ->except(['show', 'create', 'edit'])
            ->parameters(['news-categories' => 'category']);

        Route::resource('news', NewsPostController::class)->except('show')->parameters(['news' => 'post']);
        Route::get('news-trash', [NewsPostController::class, 'trash'])->name('news.trash');
        Route::post('news/{post}/restore', [NewsPostController::class, 'restore'])->name('news.restore')->withTrashed();
        Route::delete('news/{post}/force', [NewsPostController::class, 'forceDelete'])->name('news.force-delete')->withTrashed();

        Route::resource('stories', StoryController::class)->except('show')->parameters(['stories' => 'story']);
        Route::get('stories-trash', [StoryController::class, 'trash'])->name('stories.trash');
        Route::post('stories/{story}/restore', [StoryController::class, 'restore'])->name('stories.restore')->withTrashed();
        Route::delete('stories/{story}/force', [StoryController::class, 'forceDelete'])->name('stories.force-delete')->withTrashed();

        Route::resource('contact', ContactController::class)
            ->only(['index', 'show', 'destroy'])
            ->parameters(['contact' => 'contactMessage']);

        Route::resource('visitor-book', VisitorBookEntryController::class)
            ->only(['index', 'show', 'destroy'])
            ->parameters(['visitor-book' => 'visitorBookEntry']);
        Route::post('visitor-book/{visitorBookEntry}/approve', [VisitorBookEntryController::class, 'approve'])->name('visitor-book.approve');
        Route::post('visitor-book/{visitorBookEntry}/reject', [VisitorBookEntryController::class, 'reject'])->name('visitor-book.reject');

        Route::resource('featured-visitors', FeaturedVisitorController::class)
            ->except('show')
            ->parameters(['featured-visitors' => 'featuredVisitor']);
        Route::get('featured-visitors-trash', [FeaturedVisitorController::class, 'trash'])->name('featured-visitors.trash');
        Route::post('featured-visitors/{featuredVisitor}/restore', [FeaturedVisitorController::class, 'restore'])->name('featured-visitors.restore')->withTrashed();
        Route::delete('featured-visitors/{featuredVisitor}/force', [FeaturedVisitorController::class, 'forceDelete'])->name('featured-visitors.force-delete')->withTrashed();

        Route::resource('certificates', CertificateController::class);
        Route::get('certificates-trash', [CertificateController::class, 'trash'])->name('certificates.trash');
        Route::post('certificates/{certificate}/restore', [CertificateController::class, 'restore'])->name('certificates.restore')->withTrashed();
        Route::delete('certificates/{certificate}/force', [CertificateController::class, 'forceDelete'])->name('certificates.force-delete')->withTrashed();
        Route::get('certificates/{certificate}/qr', [CertificateController::class, 'qr'])->name('certificates.qr');

        Route::resource('donations', DonationController::class)->except('show');
        Route::get('donations-trash', [DonationController::class, 'trash'])->name('donations.trash');
        Route::post('donations/{donation}/restore', [DonationController::class, 'restore'])->name('donations.restore')->withTrashed();
        Route::delete('donations/{donation}/force', [DonationController::class, 'forceDelete'])->name('donations.force-delete')->withTrashed();
        Route::post('donations/{donation}/resend-receipt', [DonationController::class, 'resendReceipt'])->name('donations.resend-receipt');

        Route::resource('students', StudentController::class)->except('show');
        Route::get('students-trash', [StudentController::class, 'trash'])->name('students.trash');
        Route::post('students/{student}/restore', [StudentController::class, 'restore'])->name('students.restore')->withTrashed();
        Route::delete('students/{student}/force', [StudentController::class, 'forceDelete'])->name('students.force-delete')->withTrashed();

        Route::resource('courses', CourseController::class)->except('show');
        Route::get('courses-trash', [CourseController::class, 'trash'])->name('courses.trash');
        Route::post('courses/{course}/restore', [CourseController::class, 'restore'])->name('courses.restore')->withTrashed();
        Route::delete('courses/{course}/force', [CourseController::class, 'forceDelete'])->name('courses.force-delete')->withTrashed();

        Route::resource('enrollments', EnrollmentController::class);
        Route::get('enrollments-trash', [EnrollmentController::class, 'trash'])->name('enrollments.trash');
        Route::post('enrollments/{enrollment}/restore', [EnrollmentController::class, 'restore'])->name('enrollments.restore')->withTrashed();
        Route::delete('enrollments/{enrollment}/force', [EnrollmentController::class, 'forceDelete'])->name('enrollments.force-delete')->withTrashed();
        Route::post('enrollments/{enrollment}/issue-certificate', [EnrollmentController::class, 'issueCertificate'])->name('enrollments.issue-certificate');
        Route::post('enrollments/{enrollment}/revoke-certificate', [EnrollmentController::class, 'revokeCertificate'])->name('enrollments.revoke-certificate');
        Route::get('enrollments/{enrollment}/qr', [EnrollmentController::class, 'qr'])->name('enrollments.qr');

        Route::prefix('pages/{page}')->name('pages.')->group(function () {
            Route::get('sections', [SectionController::class, 'index'])->name('sections.index');
            Route::get('sections/create', [SectionController::class, 'create'])->name('sections.create');
            Route::post('sections', [SectionController::class, 'store'])->name('sections.store');
            Route::post('sections/reorder', [SectionController::class, 'reorder'])->name('sections.reorder');
            Route::get('sections/{section}/edit', [SectionController::class, 'edit'])->name('sections.edit');
            Route::put('sections/{section}', [SectionController::class, 'update'])->name('sections.update');
            Route::delete('sections/{section}', [SectionController::class, 'destroy'])->name('sections.destroy');

            Route::post('sections/{section}/items', [SectionItemController::class, 'store'])->name('sections.items.store');
            Route::put('sections/{section}/items/{item}', [SectionItemController::class, 'update'])->name('sections.items.update');
            Route::delete('sections/{section}/items/{item}', [SectionItemController::class, 'destroy'])->name('sections.items.destroy');
        });
    });
