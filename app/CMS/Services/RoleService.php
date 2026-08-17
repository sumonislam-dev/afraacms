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
        $this->logPermissions($role, $data['permissions'] ?? []);

        return $role;
    }

    /**
     * Update an existing role's name and permissions.
     */
    public function update(Role $role, array $data): Role
    {
        $role->update(['name' => $data['name']]);

        $role->syncPermissions($data['permissions'] ?? []);
        $this->logPermissions($role, $data['permissions'] ?? []);

        return $role;
    }

    /**
     * Record the role's resulting permission set in the activity log.
     *
     * Role::getActivitylogOptions() only logs the "name" attribute (see
     * that method's comment) - permission syncing is a pivot-table
     * relation, not a fillable attribute, so Spatie's activitylog trait
     * never captures it automatically. Given Admin can now grant/revoke
     * permissions on roles (subject to the permission-subset check in
     * StoreRoleRequest/UpdateRoleRequest), this keeps who-granted-what
     * auditable.
     */
    private function logPermissions(Role $role, array $permissions): void
    {
        activity()
            ->performedOn($role)
            ->withProperties(['permissions' => $permissions])
            ->log('permissions updated');
    }
}
