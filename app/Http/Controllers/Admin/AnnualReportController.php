<?php

namespace App\Http\Controllers\Admin;

use App\CMS\Services\AnnualReportService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAnnualReportRequest;
use App\Http\Requests\Admin\UpdateAnnualReportRequest;
use App\Models\AnnualReport;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AnnualReportController extends Controller
{
    public function __construct(private readonly AnnualReportService $reports)
    {
        $this->authorizeResource(AnnualReport::class, 'annualReport');
    }

    /**
     * Display every annual report, newest year first.
     */
    public function index(): View
    {
        $reports = AnnualReport::query()
            ->orderByDesc('year')
            ->orderBy('sort_order')
            ->paginate(15);

        return view('admin.annual-reports.index', compact('reports'));
    }

    /**
     * Show the form for creating a new report.
     */
    public function create(): View
    {
        return view('admin.annual-reports.create');
    }

    /**
     * Store a newly created report.
     */
    public function store(StoreAnnualReportRequest $request): RedirectResponse
    {
        $this->reports->create($request->validated());

        return redirect()->route('admin.annual-reports.index')->with('success', __('Annual report added successfully.'));
    }

    /**
     * Show the form for editing the given report.
     */
    public function edit(AnnualReport $annualReport): View
    {
        return view('admin.annual-reports.edit', ['report' => $annualReport]);
    }

    /**
     * Update the given report.
     */
    public function update(UpdateAnnualReportRequest $request, AnnualReport $annualReport): RedirectResponse
    {
        $this->reports->update($annualReport, $request->validated());

        return redirect()->route('admin.annual-reports.index')->with('success', __('Annual report updated successfully.'));
    }

    /**
     * Delete the given report.
     */
    public function destroy(AnnualReport $annualReport): RedirectResponse
    {
        $this->reports->delete($annualReport);

        return redirect()->route('admin.annual-reports.index')->with('success', __('Annual report deleted successfully.'));
    }
}
