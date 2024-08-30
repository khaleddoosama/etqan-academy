<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\RolePermissionService;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Yoeunes\Toastr\Facades\Toastr;

class RolePermissionController extends Controller
{
    protected RolePermissionService $RolePermissionService;

    public function __construct(RolePermissionService $RolePermissionService)
    {
        $this->RolePermissionService = $RolePermissionService;

        // $this->middleware('permission:role_permission.list')->only('index');
        // $this->middleware('permission:role_permission.create')->only('create', 'store');
        // $this->middleware('permission:role_permission.edit')->only('edit', 'update');
        // $this->middleware('permission:role_permission.delete')->only('destroy');
    }

    public function index()
    {
        $roles = $this->RolePermissionService->getAllRoles();
        return view('admin.role_permission.index', compact('roles'));
    }

    public function edit($id)
    {
        $role = $this->RolePermissionService->getRole($id);

        $permission_modules = $this->RolePermissionService->getPermissionModules();
        // return($permission_modules);
        return view('admin.role_permission.edit', compact('role', 'permission_modules'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'permissions' => 'array|required|min:1',
        ]);

        $role = $this->RolePermissionService->getRole($id);

        $this->RolePermissionService->updateRolePermissions($data, $role) ? Toastr::success(__('messages.role_permission_updated'), __('status.success')) : '';

        return redirect()->route('admin.role_permissions.index');
    }
}
