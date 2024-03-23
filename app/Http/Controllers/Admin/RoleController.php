<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RoleRequest;
use App\Services\RoleService;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Yoeunes\Toastr\Facades\Toastr;

class RoleController extends Controller
{
    // constructor
    protected RoleService $RoleService;

    public function __construct(RoleService $RoleService)
    {
        $this->RoleService = $RoleService;

        $this->middleware('permission:role.list')->only('index');
        $this->middleware('permission:role.create')->only('create', 'store');
        $this->middleware('permission:role.edit')->only('edit', 'update');
        $this->middleware('permission:role.delete')->only('destroy');
    }

    // index
    public function index()
    {
        $roles = $this->RoleService->getAllRoles();
        return view('admin.role.index', compact('roles'));
    }

    // create
    public function create()
    {
        return view('admin.role.create');
    }

    // store
    public function store(RoleRequest $request)
    {
        $data = $request->validated();

        $this->RoleService->createRole($data);

        Toastr::success(__('messages.role_created'), __('status.success'));

        return redirect()->route('admin.role.index');
    }

    // edit
    public function edit(Role $role)
    {
        return view('admin.role.edit', compact('role'));
    }

    // update
    public function update(RoleRequest $request, Role $role)
    {

        $data = $request->validated();

        $this->RoleService->updateRole($role, $data) ? Toastr::success(__('messages.role_updated'), __('status.success')) : '';

        return redirect()->route('admin.role.index');
    }

    // destroy
    public function destroy(Role $role)
    {
        $this->RoleService->deleteRole($role);

        Toastr::success(__('messages.role_deleted'), __('status.success'));

        return redirect()->route('admin.role.index');
    }
}
