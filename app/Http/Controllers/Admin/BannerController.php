<?php

namespace App\Http\Controllers\Admin;

use App\CMS\Services\BannerService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBannerRequest;
use App\Http\Requests\Admin\UpdateBannerRequest;
use App\Models\Banner;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BannerController extends Controller
{
    public function __construct(private readonly BannerService $banners)
    {
        $this->authorizeResource(Banner::class, 'banner');
    }

    /**
     * Display every banner, grouped by placement type.
     */
    public function index(): View
    {
        $banners = Banner::query()->orderBy('type')->orderBy('sort_order')->get()->groupBy('type');

        return view('admin.banners.index', compact('banners'));
    }

    /**
     * Show the form for creating a new banner.
     */
    public function create(): View
    {
        return view('admin.banners.create', ['type' => request('type')]);
    }

    /**
     * Store a newly created banner.
     */
    public function store(StoreBannerRequest $request): RedirectResponse
    {
        $this->banners->create($request->validated());

        return redirect()->route('admin.banners.index')->with('success', __('Banner created successfully.'));
    }

    /**
     * Show the form for editing the given banner.
     */
    public function edit(Banner $banner): View
    {
        return view('admin.banners.edit', compact('banner'));
    }

    /**
     * Update the given banner.
     */
    public function update(UpdateBannerRequest $request, Banner $banner): RedirectResponse
    {
        $this->banners->update($banner, $request->validated());

        return redirect()->route('admin.banners.index')->with('success', __('Banner updated successfully.'));
    }

    /**
     * Delete the given banner.
     */
    public function destroy(Banner $banner): RedirectResponse
    {
        $this->banners->delete($banner);

        return redirect()->route('admin.banners.index')->with('success', __('Banner deleted successfully.'));
    }
}
