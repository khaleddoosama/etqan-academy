<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProgramRequest;
use App\Models\Program;
use App\Services\ProgramService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Yoeunes\Toastr\Facades\Toastr;


class ProgramController extends Controller
{

    protected ProgramService $programService;

    public function __construct(ProgramService $programService)
    {
        $this->programService = $programService;

        // program.list program.create program.edit program.delete
        $this->middleware('permission:program.list')->only('index');
        $this->middleware('permission:program.create')->only('create', 'store');
        $this->middleware('permission:program.edit')->only('edit', 'update');
        $this->middleware('permission:program.delete')->only('destroy');
    }

    public function index(): View
    {
        $programs = $this->programService->getPrograms();
        return view('admin.program.index', compact('programs'));
    }


    public function create(): View
    {
        return view('admin.program.create');
    }


    public function store(ProgramRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $this->programService->createProgram($data);

        Toastr::success(__('messages.program_created'), __('status.success'));
        return redirect()->route('admin.programs.index');
    }





    public function edit(Program $program): View
    {
        return view('admin.program.edit', compact('program'));
    }



    public function update(Program $program, ProgramRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $this->programService->updateProgram($program, $data) ?
            Toastr::success(__('messages.program_updated'), __('status.success')) :
            '';

        return redirect()->route('admin.programs.index');
    }

    public function destroy(Program $program): RedirectResponse
    {
        $this->programService->deleteProgram($program) ?
            Toastr::success(__('messages.program_deleted'), __('status.success')) :
            '';


        return redirect()->route('admin.programs.index');
    }
}
