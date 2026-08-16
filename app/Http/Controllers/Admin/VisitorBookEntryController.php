<?php

namespace App\Http\Controllers\Admin;

use App\CMS\Services\VisitorBookService;
use App\Http\Controllers\Controller;
use App\Models\VisitorBookEntry;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class VisitorBookEntryController extends Controller
{
    public function __construct(private readonly VisitorBookService $visitorBook)
    {
        $this->authorizeResource(VisitorBookEntry::class, 'visitorBookEntry');
    }

    /**
     * Display a listing of visitor book entries, optionally filtered by status.
     */
    public function index(): View
    {
        $status = request('status', 'pending');

        $entries = VisitorBookEntry::with('project:id,title,slug')
            ->when($status !== 'all', fn ($query) => $query->where('status', $status))
            ->when(request('search'), fn ($query, $search) => $query->where('visitor_name', 'like', "%{$search}%"))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $counts = [
            'pending' => VisitorBookEntry::where('status', 'pending')->count(),
            'approved' => VisitorBookEntry::where('status', 'approved')->count(),
            'rejected' => VisitorBookEntry::where('status', 'rejected')->count(),
        ];

        return view('admin.visitor-book.index', compact('entries', 'status', 'counts'));
    }

    /**
     * Display a single visitor book entry.
     */
    public function show(VisitorBookEntry $visitorBookEntry): View
    {
        return view('admin.visitor-book.show', ['entry' => $visitorBookEntry]);
    }

    /**
     * Approve the given entry, making it visible publicly.
     */
    public function approve(VisitorBookEntry $visitorBookEntry): RedirectResponse
    {
        $this->authorize('update', $visitorBookEntry);

        $this->visitorBook->approve($visitorBookEntry);

        return back()->with('success', __('Entry approved and now visible publicly.'));
    }

    /**
     * Reject the given entry, keeping it hidden from public view.
     */
    public function reject(VisitorBookEntry $visitorBookEntry): RedirectResponse
    {
        $this->authorize('update', $visitorBookEntry);

        $this->visitorBook->reject($visitorBookEntry);

        return back()->with('success', __('Entry rejected.'));
    }

    /**
     * Permanently delete the given entry.
     */
    public function destroy(VisitorBookEntry $visitorBookEntry): RedirectResponse
    {
        $this->visitorBook->delete($visitorBookEntry);

        return redirect()->route('admin.visitor-book.index')->with('success', __('Entry deleted successfully.'));
    }
}
