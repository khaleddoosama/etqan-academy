<?php

namespace App\Http\Controllers\Admin;

use App\Events\NewCourseEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CourseRequest;
use App\Models\Course;
use App\Services\CategoryService;
use App\Services\CourseService;
use App\Services\InstructorService;
use App\Services\ProgramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\In;
use Yoeunes\Toastr\Facades\Toastr;

class CourseController extends Controller
{

    protected CourseService $courseService;
    protected CategoryService $categoryService;
    protected ProgramService $programService;
    protected InstructorService $instructorService;

    public function __construct(
        CourseService $courseService,
        CategoryService $categoryService,
        ProgramService $programService,
        InstructorService $instructorService,
    ) {
        $this->courseService = $courseService;
        $this->categoryService = $categoryService;
        $this->programService = $programService;
        $this->instructorService = $instructorService;

        // course.list course.create course.edit course.delete
        $this->middleware('permission:course.list')->only('index');
        $this->middleware('permission:course.create')->only('create', 'store');
        $this->middleware('permission:course.edit')->only('edit', 'update');
        $this->middleware('permission:course.delete')->only('destroy');
    }

    public function index()
    {
        $courses = $this->courseService->getCourses();

        return view('admin.course.index', compact('courses'));
    }

    public function create()
    {
        $categories = $this->categoryService->getCategories();
        $programs = $this->programService->getPrograms();
        $instructors = $this->instructorService->getInstructors();
        return view('admin.course.create', compact('categories', 'programs', 'instructors'));
    }

    public function store(CourseRequest $request)
    {
        $data = $request->validated();

        $course = $this->courseService->createCourse($data);

        // send email
        event(new NewCourseEvent([$course->id], ['courseSlug' => $course->slug, 'course_title' => $course->title]));


        Toastr::success(__('messages.course_created'), __('status.success'));

        return redirect()->route('admin.courses.index');
    }


    public function edit(Course $course)
    {
        $course->load('sections');
        $categories = $this->categoryService->getCategories();
        $programs = $this->programService->getPrograms();
        $instructors = $this->instructorService->getInstructors();
        $courses = $this->courseService->getCourses();
        return view('admin.course.edit', compact('course', 'categories', 'programs', 'instructors', 'courses'));
    }

    public function update(CourseRequest $request, Course $course)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();

            $this->courseService->updateCourse($course, $data) ? Toastr::success(__('messages.course_updated'), __('status.success')) : '';

            DB::commit();
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            Toastr::error($e->getMessage());
            return redirect()->back();
        }
    }

    public function destroy(Course $course)
    {
        $this->courseService->deleteCourse($course) ? Toastr::success(__('messages.course_deleted'), __('status.success')) : '';
        return redirect()->back();
    }

    // status
    public function status(Request $request, $id)
    {
        $data = $request->validate([
            'status' => 'required',
        ]);

        $this->courseService->changeStatus($request->status, $id) ?
            Toastr::success(__('messages.course_status_updated'), __('status.success'))
            : Toastr::error(__('messages.something_went_wrong'), __('status.error'));

        return redirect()->back();
    }
}
