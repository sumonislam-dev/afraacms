<?php

namespace App\Http\Controllers\Admin;

use App\CMS\Services\TeamService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTeamMemberRequest;
use App\Http\Requests\Admin\UpdateTeamMemberRequest;
use App\Models\TeamMember;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TeamMemberController extends Controller
{
    public function __construct(private readonly TeamService $team)
    {
        $this->authorizeResource(TeamMember::class, 'team');
    }

    /**
     * Display a listing of the team members.
     */
    public function index(): View
    {
        $members = TeamMember::with('category')
            ->when(request('search'), fn ($query, $search) => $query->where('name', 'like', "%{$search}%"))
            ->orderBy('sort_order')
            ->paginate(15)
            ->withQueryString();

        return view('admin.team.index', compact('members'));
    }

    /**
     * Show the form for creating a new team member.
     */
    public function create(): View
    {
        return view('admin.team.create');
    }

    /**
     * Store a newly created team member.
     */
    public function store(StoreTeamMemberRequest $request): RedirectResponse
    {
        $this->team->create($request->validated());

        return redirect()->route('admin.team.index')->with('success', __('Team member added successfully.'));
    }

    /**
     * Show the form for editing the given team member.
     */
    public function edit(TeamMember $team): View
    {
        return view('admin.team.edit', ['member' => $team]);
    }

    /**
     * Update the given team member.
     */
    public function update(UpdateTeamMemberRequest $request, TeamMember $team): RedirectResponse
    {
        $this->team->update($team, $request->validated());

        return redirect()->route('admin.team.index')->with('success', __('Team member updated successfully.'));
    }

    /**
     * Delete the given team member.
     */
    public function destroy(TeamMember $team): RedirectResponse
    {
        $this->team->delete($team);

        return redirect()->route('admin.team.index')->with('success', __('Team member deleted successfully.'));
    }

    /**
     * Display the trashed (soft-deleted) team members.
     */
    public function trash(): View
    {
        $this->authorize('viewAny', TeamMember::class);

        $members = TeamMember::onlyTrashed()
            ->with('category')
            ->when(request('search'), fn ($query, $search) => $query->where('name', 'like', "%{$search}%"))
            ->orderByDesc('deleted_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.team.trash', compact('members'));
    }

    /**
     * Restore a trashed team member.
     */
    public function restore(TeamMember $team): RedirectResponse
    {
        $this->authorize('restore', $team);

        $this->team->restore($team);

        return redirect()->route('admin.team.trash')->with('success', __('Team member restored successfully.'));
    }

    /**
     * Permanently delete a trashed team member.
     */
    public function forceDelete(TeamMember $team): RedirectResponse
    {
        $this->authorize('forceDelete', $team);

        $this->team->forceDelete($team);

        return redirect()->route('admin.team.trash')->with('success', __('Team member permanently deleted.'));
    }
}
