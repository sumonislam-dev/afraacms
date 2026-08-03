<?php

namespace App\Http\Controllers\Admin;

use App\CMS\Services\RoleService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRoleRequest;
use App\Http\Requests\Admin\UpdateRoleRequest;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function __construct(private readonly RoleService $roles)
    {
        $this->authorizeResource(Role::class, 'role');
    }

    /**
     * Display a listing of the roles, including their assigned user count.
     */
    public function index(): View
    {
        $roles = Role::withCount(['users', 'permissions'])
            ->when(request('search'), fn ($query, $search) => $query->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new role.
     */
    public function create(): View
    {
        $permissionsByModule = $this->permissionsByModule();

        return view('admin.roles.create', [
            'permissionsByModule' => $permissionsByModule,
            'assigned' => [],
        ]);
    }

    /**
     * Store a newly created role.
     */
    public function store(StoreRoleRequest $request): RedirectResponse
    {
        $this->roles->create($request->validated());

        return redirect()->route('admin.roles.index')->with('success', __('Role created successfully.'));
    }

    /**
     * Show the form for editing the given role.
     */
    public function edit(Role $role): View
    {
        return view('admin.roles.edit', [
            'role' => $role,
            'permissionsByModule' => $this->permissionsByModule(),
            'assigned' => $role->permissions->pluck('name')->all(),
        ]);
    }

    /**
     * Update the given role.
     */
    public function update(UpdateRoleRequest $request, Role $role): RedirectResponse
    {
        $this->roles->update($role, $request->validated());

        return redirect()->route('admin.roles.index')->with('success', __('Role updated successfully.'));
    }

    /**
     * Remove the given role.
     */
    public function destroy(Role $role): RedirectResponse
    {
        $role->delete();

        return redirect()->route('admin.roles.index')->with('success', __('Role deleted successfully.'));
    }

    /**
     * Get all permissions grouped by their module prefix (e.g. "pages.create" -> "pages").
     */
    private function permissionsByModule()
    {
        return Permission::orderBy('name')->get()->groupBy(
            fn (Permission $permission) => Str::before($permission->name, '.')
        );
    }
}
