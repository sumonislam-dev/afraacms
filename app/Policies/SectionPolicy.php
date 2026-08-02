<?php

namespace App\Policies;

use App\Models\Section;
use App\Models\User;

class SectionPolicy
{
    /**
     * Determine whether the user can view a page's sections.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('sections.view');
    }

    /**
     * Determine whether the user can view the given section.
     */
    public function view(User $user, Section $section): bool
    {
        return $user->can('sections.view');
    }

    /**
     * Determine whether the user can add sections to a page.
     */
    public function create(User $user): bool
    {
        return $user->can('sections.create');
    }

    /**
     * Determine whether the user can update the given section (including its items).
     */
    public function update(User $user, Section $section): bool
    {
        return $user->can('sections.edit');
    }

    /**
     * Determine whether the user can delete the given section.
     */
    public function delete(User $user, Section $section): bool
    {
        return $user->can('sections.delete');
    }
}
