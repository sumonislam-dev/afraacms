<?php

use App\Http\Controllers\Admin\ActivityController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\GalleryItemController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\MenuItemController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ProjectCategoryController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SectionController;
use App\Http\Controllers\Admin\SectionItemController;
use App\Http\Controllers\Admin\SeoController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
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

        Route::resource('contact', ContactController::class)
            ->only(['index', 'show', 'destroy'])
            ->parameters(['contact' => 'contactMessage']);

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
