<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CourseRequest;
use App\Models\Category;
use App\Models\Course;
use App\Services\CategoryService;
use App\Services\CourseService;
use Illuminate\Http\Request;
use Yoeunes\Toastr\Facades\Toastr;

class CourseController extends Controller
{

    protected CourseService $courseService;
    protected CategoryService $categoryService;

    public function __construct(CourseService $courseService, CategoryService $categoryService)
    {
        $this->courseService = $courseService;
        $this->categoryService = $categoryService;
        // course.list course.create course.edit course.delete
        // $this->middleware('permission:course.list')->only('index');
        // $this->middleware('permission:course.create')->only('create', 'store');
        // $this->middleware('permission:course.edit')->only('edit', 'update');
        // $this->middleware('permission:course.delete')->only('destroy');
    }

    public function index()
    {
        $courses = $this->courseService->getCourses();

        return view('admin.course.index', compact('courses'));
    }

    public function create()
    {
        $categories = $this->categoryService->getCategories();
        return view('admin.course.create', compact('categories'));
    }

    public function store(CourseRequest $request)
    {
        $data = $request->validated();

        $this->courseService->createCourse($data);

        Toastr::success(__('messages.course_created'), __('status.success'));

        return redirect()->route('admin.courses.index');
    }


    public function edit(Course $course)
    {
        $course->load('sections');
        $categories = $this->categoryService->getCategories();
        return view('admin.course.edit', compact('course', 'categories'));
    }

    public function update(CourseRequest $request, Course $course)
    {
        $data = $request->validated();

        $this->courseService->updateCourse($course, $data) ? Toastr::success(__('messages.course_updated'), __('status.success')) : '';

        return redirect()->back();
    }

    public function destroy(string $id)
    {
        //
    }
}
