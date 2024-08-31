<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PermissionService;
use App\Services\RolePermissionService;
use App\Services\RoleService;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Yoeunes\Toastr\Facades\Toastr;

class RolePermissionController extends Controller
{
    protected RolePermissionService $RolePermissionService;
    protected PermissionService $permissionService;
    protected RoleService $roleService;

    public function __construct(RolePermissionService $RolePermissionService, PermissionService $permissionService, RoleService $roleService)
    {
        $this->RolePermissionService = $RolePermissionService;
        $this->permissionService = $permissionService;
        $this->roleService = $roleService;

        $this->middleware('permission:role_permission.list')->only('index');
        $this->middleware('permission:role_permission.edit')->only('edit', 'update');
    }

    public function index()
    {
        $roles = $this->roleService->getAllRoles();
        return view('admin.role_permission.index', compact('roles'));
    }

    public function edit($id)
    {
        $role = $this->roleService->getRole($id);

        $permission_modules = $this->permissionService->getPermissionModules();
        // return($permission_modules);
        return view('admin.role_permission.edit', compact('role', 'permission_modules'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'permissions' => 'array|required|min:1',
        ]);

        $role = $this->roleService->getRole($id);

        $this->RolePermissionService->updateRolePermissions($data, $role) ? Toastr::success(__('messages.role_permission_updated'), __('status.success')) : '';

        return redirect()->route('admin.role_permissions.index');
    }
}
