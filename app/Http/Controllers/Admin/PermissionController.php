<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PermissionController extends Controller
{
    /**
     * Display the read-only, module-grouped permission list.
     */
    public function index(): View
    {
        $this->authorize('viewAny', Permission::class);

        $permissionsByModule = Permission::orderBy('name')->get()->groupBy(
            fn (Permission $permission) => Str::before($permission->name, '.')
        );

        return view('admin.permissions.index', compact('permissionsByModule'));
    }
}
