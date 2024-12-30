<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StudentWorkRequest;
use App\Services\StudentWorkCategoryService;
use App\Services\StudentWorkService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Yoeunes\Toastr\Facades\Toastr;

class StudentWorkController extends Controller
{

    public $studentWorkService;
    public $studentWorkCategoryService;

    public function __construct(StudentWorkService $studentWorkService, StudentWorkCategoryService $studentWorkCategoryService)
    {
        $this->studentWorkService = $studentWorkService;
        $this->studentWorkCategoryService = $studentWorkCategoryService;

        // student_work.list student_work.create student_work.edit student_work.delete
        $this->middleware('permission:student_work.list')->only('index');
        $this->middleware('permission:student_work.create')->only('create', 'store');
        $this->middleware('permission:student_work.edit')->only('updateOrder');
        $this->middleware('permission:student_work.delete')->only('destroy');
    }

    public function index()
    {
        $studentWorks = $this->studentWorkService->getStudentWorks();
        $studentWorkCategories = $this->studentWorkCategoryService->getStudentWorkCategories();

        return view('admin.student_works.index', compact(['studentWorks', 'studentWorkCategories']));
    }

    public function create()
    {
        $studentWorkCategories = $this->studentWorkCategoryService->getStudentWorkCategories();

        return view('admin.student_works.create', compact('studentWorkCategories'));
    }

    public function store(StudentWorkRequest $request)
    {
        $data = $request->validated();

        $this->studentWorkService->createStudentWork($data);

        Toastr::success(__('messages.student_work_created'), __('status.success'));
        return redirect()->route('admin.student_works.index');
    }

    public function destroy($id)
    {
        $this->studentWorkService->deleteStudentWork($id);
        Toastr::success(__('messages.student_work_deleted'), __('status.success'));
        return redirect()->route('admin.student_works.index');
    }

    public function updateOrder(Request $request)
    {

        $studentWorks = $request->get('student_works');

        $this->studentWorkService->updateOrder($studentWorks);

        return response()->json([
            'message' => __('messages.student_works_order_updated'),
            'status' => 200
        ]);
    }
}
