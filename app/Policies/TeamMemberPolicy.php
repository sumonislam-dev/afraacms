<?php

namespace App\Policies;

use App\Models\TeamMember;
use App\Models\User;

class TeamMemberPolicy
{
    /**
     * Determine whether the user can view the list of team members.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('team.view');
    }

    /**
     * Determine whether the user can view the given team member.
     */
    public function view(User $user, TeamMember $member): bool
    {
        return $user->can('team.view');
    }

    /**
     * Determine whether the user can create team members.
     */
    public function create(User $user): bool
    {
        return $user->can('team.create');
    }

    /**
     * Determine whether the user can update the given team member.
     */
    public function update(User $user, TeamMember $member): bool
    {
        return $user->can('team.edit');
    }

    /**
     * Determine whether the user can delete the given team member.
     */
    public function delete(User $user, TeamMember $member): bool
    {
        return $user->can('team.delete');
    }

    /**
     * Determine whether the user can restore the given trashed team member.
     */
    public function restore(User $user, TeamMember $member): bool
    {
        return $user->can('team.delete');
    }

    /**
     * Determine whether the user can permanently delete the given trashed team member.
     */
    public function forceDelete(User $user, TeamMember $member): bool
    {
        return $user->can('team.delete');
    }
}
