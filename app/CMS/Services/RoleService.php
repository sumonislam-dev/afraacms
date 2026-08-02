<?php

namespace App\CMS\Services;

use App\Models\Role;

class RoleService
{
    /**
     * Create a new role and assign its permissions.
     */
    public function create(array $data): Role
    {
        $role = Role::create([
            'name' => $data['name'],
            'guard_name' => 'web',
        ]);

        $role->syncPermissions($data['permissions'] ?? []);

        return $role;
    }

    /**
     * Update an existing role's name and permissions.
     */
    public function update(Role $role, array $data): Role
    {
        $role->update(['name' => $data['name']]);

        $role->syncPermissions($data['permissions'] ?? []);

        return $role;
    }
}
