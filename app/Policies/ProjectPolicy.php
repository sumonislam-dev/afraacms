<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    /**
     * Determine whether the user can view the list of projects.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('projects.view');
    }

    /**
     * Determine whether the user can view the given project.
     */
    public function view(User $user, Project $project): bool
    {
        return $user->can('projects.view');
    }

    /**
     * Determine whether the user can create projects.
     */
    public function create(User $user): bool
    {
        return $user->can('projects.create');
    }

    /**
     * Determine whether the user can update the given project.
     */
    public function update(User $user, Project $project): bool
    {
        return $user->can('projects.edit');
    }

    /**
     * Determine whether the user can delete the given project.
     */
    public function delete(User $user, Project $project): bool
    {
        return $user->can('projects.delete');
    }
}
