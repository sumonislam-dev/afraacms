<?php

namespace App\Http\Controllers\Admin;

use App\CMS\Services\DonationService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreDonationRequest;
use App\Http\Requests\Admin\UpdateDonationRequest;
use App\Models\Donation;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DonationController extends Controller
{
    public function __construct(private readonly DonationService $donations)
    {
        $this->authorizeResource(Donation::class, 'donation');
    }

    /**
     * Display a listing of the donations.
     */
    public function index(): View
    {
        $donations = Donation::with('project')
            ->when(request('search'), fn ($query, $search) => $query
                ->where('donor_name', 'like', "%{$search}%")
                ->orWhere('receipt_number', 'like', "%{$search}%"))
            ->latest('donated_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.donations.index', compact('donations'));
    }

    /**
     * Show the form for creating a new donation.
     */
    public function create(): View
    {
        return view('admin.donations.create');
    }

    /**
     * Store a newly created donation.
     */
    public function store(StoreDonationRequest $request): RedirectResponse
    {
        $this->donations->create($request->validated());

        return redirect()->route('admin.donations.index')->with('success', __('Donation recorded successfully.'));
    }

    /**
     * Show the form for editing the given donation.
     */
    public function edit(Donation $donation): View
    {
        return view('admin.donations.edit', compact('donation'));
    }

    /**
     * Update the given donation.
     */
    public function update(UpdateDonationRequest $request, Donation $donation): RedirectResponse
    {
        $this->donations->update($donation, $request->validated());

        return redirect()->route('admin.donations.index')->with('success', __('Donation updated successfully.'));
    }

    /**
     * Delete the given donation.
     */
    public function destroy(Donation $donation): RedirectResponse
    {
        $this->donations->delete($donation);

        return redirect()->route('admin.donations.index')->with('success', __('Donation deleted successfully.'));
    }

    /**
     * Display the trashed (soft-deleted) donations.
     */
    public function trash(): View
    {
        $this->authorize('viewAny', Donation::class);

        $donations = Donation::onlyTrashed()
            ->with('project')
            ->when(request('search'), fn ($query, $search) => $query->where('donor_name', 'like', "%{$search}%"))
            ->orderByDesc('deleted_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.donations.trash', compact('donations'));
    }

    /**
     * Restore a trashed donation.
     */
    public function restore(Donation $donation): RedirectResponse
    {
        $this->authorize('restore', $donation);

        $this->donations->restore($donation);

        return redirect()->route('admin.donations.trash')->with('success', __('Donation restored successfully.'));
    }

    /**
     * Permanently delete a trashed donation.
     */
    public function forceDelete(Donation $donation): RedirectResponse
    {
        $this->authorize('forceDelete', $donation);

        $this->donations->forceDelete($donation);

        return redirect()->route('admin.donations.trash')->with('success', __('Donation permanently deleted.'));
    }

    /**
     * Resend the receipt email for a donation.
     */
    public function resendReceipt(Donation $donation): RedirectResponse
    {
        $this->authorize('update', $donation);

        if (! $donation->donor_email) {
            return back()->with('error', __('This donation has no donor email on file.'));
        }

        $sent = $this->donations->sendReceipt($donation);

        return back()->with(
            $sent ? 'success' : 'error',
            $sent ? __('Receipt resent successfully.') : __('Failed to send the receipt. Check the mail configuration.')
        );
    }
}
