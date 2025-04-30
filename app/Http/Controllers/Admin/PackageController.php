<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PackageRequest;
use App\Services\PackageService;
use App\Services\ProgramService;
use Illuminate\Http\Request;
use Yoeunes\Toastr\Facades\Toastr;

class PackageController extends Controller
{

    public function __construct(
        private PackageService $packageService,
        private ProgramService $programService
    ) {

        // package.list package.create package.edit package.delete
        $this->middleware('permission:package.list')->only('index');
        $this->middleware('permission:package.create')->only('create', 'store');
        $this->middleware('permission:package.edit')->only('edit', 'update');
        $this->middleware('permission:package.delete')->only('destroy');
    }

    public function index()
    {
        $packages = $this->packageService->getAll();

        return view('admin.package.index', compact('packages'));
    }


    public function create()
    {
        $programs = $this->programService->getAll();
        return view('admin.package.create', compact('programs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PackageRequest $request)
    {
        $data = $request->validated();

        $this->packageService->store($data);

        Toastr::success(__('messages.package_created'), __('status.success'));

        return redirect()->route('admin.packages.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $package = $this->packageService->find($id);
        $programs = $this->programService->getAll();

        return view('admin.package.edit', compact('package', 'programs'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PackageRequest $request, string $id)
    {
        $data = $request->validated();

        $this->packageService->update($data, $id);

        Toastr::success(__('messages.package_updated'), __('status.success'));
        return redirect()->route('admin.packages.edit', $id);

        return redirect()->route('admin.packages.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->packageService->delete($id);

        Toastr::success(__('messages.package_deleted'), __('status.success'));

        return redirect()->route('admin.packages.index');
    }
}
