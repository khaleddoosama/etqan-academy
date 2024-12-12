<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CourseInstallmentRequest;
use App\Models\CourseInstallment;
use App\Services\CourseInstallmentService;
use App\Services\CourseService;
use Illuminate\Http\Request;
use Yoeunes\Toastr\Facades\Toastr;

class CourseInstallmentController extends Controller
{
    protected CourseService $courseService;
    protected CourseInstallmentService $courseInstallmentService;

    public function __construct(CourseService $courseService, CourseInstallmentService $courseInstallmentService)
    {
        $this->courseService = $courseService;
        $this->courseInstallmentService = $courseInstallmentService;

        // course_installment.list course_installment.create course_installment.edit course_installment.delete
        $this->middleware('permission:course_installment.list')->only('index');
        $this->middleware('permission:course_installment.create')->only('create', 'store');
        $this->middleware('permission:course_installment.edit')->only('edit', 'update');
        $this->middleware('permission:course_installment.delete')->only('destroy');
    }
    public function index()
    {
        $courseInstallments = $this->courseInstallmentService->getAll();
        return view('admin.course_installment.index', compact('courseInstallments'));
    }


    public function create()
    {
        $courses = $this->courseService->getCourses();
        return view('admin.course_installment.create', compact('courses'));
    }

    public function store(CourseInstallmentRequest $request)
    {
        $data = $request->validated();
        $this->courseInstallmentService->createCourseInstallment($data);
        Toastr::success(__('messages.course_installment_created'), __('status.success'));

        return redirect()->route('admin.course_installments.index');
    }


    public function show(CourseInstallment $courseInstallment) {}


    public function edit(CourseInstallment $courseInstallment)
    {
        $courses = $this->courseService->getCourses();
        return view('admin.course_installment.edit', compact('courseInstallment', 'courses'));
    }


    public function update(CourseInstallmentRequest $request, CourseInstallment $courseInstallment)
    {
        $data = $request->validated();
        $is_changed = $this->courseInstallmentService->updateCourseInstallment($courseInstallment, $data);
        $is_changed ? Toastr::success(__('messages.course_installment_updated'), __('status.success')) : '';

        return redirect()->route('admin.course_installments.index');
    }


    public function destroy(CourseInstallment $courseInstallment) {
        $result = $this->courseInstallmentService->deleteCourseInstallment($courseInstallment);
        $result ? Toastr::success(__('messages.course_installment_deleted'), __('status.success')) : '';

        return redirect()->route('admin.course_installments.index');
    }
}
