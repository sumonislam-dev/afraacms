<?php

namespace App\CMS\Services;

use App\Models\User;

class UserService
{
    /**
     * Create a new admin user and assign their role.
     */
    public function create(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'is_active' => $data['is_active'] ?? true,
            'email_verified_at' => now(),
        ]);

        $user->syncRoles([$data['role']]);

        return $user;
    }

    /**
     * Update an existing admin user's details and role.
     */
    public function update(User $user, array $data): User
    {
        $user->fill([
            'name' => $data['name'],
            'email' => $data['email'],
            'is_active' => $data['is_active'] ?? $user->is_active,
        ]);

        if (! empty($data['password'])) {
            $user->password = $data['password'];
        }

        $user->save();

        $user->syncRoles([$data['role']]);

        return $user;
    }

    /**
     * Toggle a user's active status.
     *
     * Refuses to deactivate the last currently-active Super Admin, so the
     * system can never end up with nobody able to manage top-level access.
     */
    public function toggleActive(User $user): bool
    {
        if ($user->is_active && $user->isLastActiveSuperAdmin()) {
            return false;
        }

        $user->update(['is_active' => ! $user->is_active]);

        return true;
    }
}
