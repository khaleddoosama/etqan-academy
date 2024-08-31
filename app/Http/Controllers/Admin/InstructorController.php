<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\InstructorRequest;
use App\Models\Instructor;
use App\Services\InstructorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Yoeunes\Toastr\Facades\Toastr;


class InstructorController extends Controller
{

    protected InstructorService $instructorService;

    public function __construct(InstructorService $instructorService)
    {
        $this->instructorService = $instructorService;

        // instructor.list instructor.create instructor.edit instructor.delete
        $this->middleware('permission:instructor.list')->only('index');
        $this->middleware('permission:instructor.create')->only('create', 'store');
        $this->middleware('permission:instructor.edit')->only('edit', 'update');
        $this->middleware('permission:instructor.delete')->only('destroy');
    }

    public function index(): View
    {
        $instructors = $this->instructorService->getInstructors();
        return view('admin.instructor.index', compact('instructors'));
    }


    public function create(): View
    {
        return view('admin.instructor.create');
    }


    public function store(InstructorRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $this->instructorService->createInstructor($data);

        Toastr::success(__('messages.instructor_created'), __('status.success'));
        return redirect()->route('admin.instructors.index');
    }

    public function edit(Instructor $instructor): View
    {
        return view('admin.instructor.edit', compact('instructor'));
    }



    public function update(Instructor $instructor, InstructorRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $this->instructorService->updateInstructor($instructor, $data) ?
            Toastr::success(__('messages.instructor_updated'), __('status.success')) :
            '';

        return redirect()->route('admin.instructors.index');
    }

    public function destroy(Instructor $instructor): RedirectResponse
    {
        $this->instructorService->deleteInstructor($instructor) ?
            Toastr::success(__('messages.instructor_deleted'), __('status.success')) :
            '';


        return redirect()->route('admin.instructors.index');
    }
}
