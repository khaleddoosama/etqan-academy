<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PermissionRequest;
use App\Services\PermissionService;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Yoeunes\Toastr\Facades\Toastr;

class PermissionController extends Controller
{
    // constructor
    protected PermissionService $PermissionService;

    public function __construct(PermissionService $PermissionService)
    {
        $this->PermissionService = $PermissionService;

        $this->middleware('permission:permission.list')->only('index');
        $this->middleware('permission:permission.create')->only('create', 'store');
        $this->middleware('permission:permission.edit')->only('edit', 'update');
        $this->middleware('permission:permission.delete')->only('destroy');
    }

    // index
    public function index()
    {
        $permissions = $this->PermissionService->getAllPermissions();
        return view('admin.permission.index', compact('permissions'));
    }

    // create
    public function create()
    {
        return view('admin.permission.create');
    }

    // store
    public function store(PermissionRequest $request)
    {
        $data = $request->validated();

        $this->PermissionService->createPermission($data);

        Toastr::success(__('messages.permission_created'), __('status.success'));

        return redirect()->route('admin.permission.index');
    }

    // edit
    public function edit(Permission $permission)
    {
        return view('admin.permission.edit', compact('permission'));
    }

    // update
    public function update(PermissionRequest $request, Permission $permission)
    {

        $data = $request->validated();

        $this->PermissionService->updatePermission($permission, $data) ? Toastr::success(__('messages.permission_updated'), __('status.success')) : '';

        return redirect()->route('admin.permission.index');
    }

    // destroy
    public function destroy(Permission $permission)
    {

        $this->PermissionService->deletePermission($permission);

        Toastr::success(__('messages.permission_deleted'), __('status.success'));

        return redirect()->route('admin.permission.index');;
    }
}
