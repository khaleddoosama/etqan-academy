<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CourseUsersRequest;
use App\Http\Requests\Admin\UserCoursesRequest;
use App\Models\Course;
use App\Models\User;
use App\Models\UserCourse;
use App\Services\UserCoursesService;
use Illuminate\Http\Request;
use Yoeunes\Toastr\Facades\Toastr;

class UserCoursesController extends Controller
{
    private $userCoursesService;
    // constructor for UserCoursesService
    public function __construct(UserCoursesService $userCoursesService)
    {
        $this->userCoursesService = $userCoursesService;
        $this->middleware('permission:user_course.list')->only('index', 'showStudents');
        $this->middleware('permission:user_course.create')->only('store', 'store2');
        $this->middleware('permission:user_course.status')->only('changeStatus');
    }

    // get user courses
    public function index(User $user)
    {
        // $user_courses = $user->courses()->get();
        $user_courses = UserCourse::where('student_id', $user->id)->with('course')->get();
        $courses = $this->userCoursesService->getCourses();
        $title = __('attributes.courses');
        return view('admin.user.courses', compact('user', 'user_courses', 'title', 'courses'));
    }

    public function showStudents(Course $course)
    {
        $course_students = UserCourse::where('course_id', $course->id)->with('student')->get();
        $title = __('attributes.students');
        $students = $this->userCoursesService->getStudents();

        return view('admin.course.show-students', compact('course_students', 'course', 'title', 'students'));
    }

    // store user courses
    public function store(UserCoursesRequest $request, User $user)
    {
        $data = $request->validated();

        $this->userCoursesService->storeUserCourse($data, $user);
        Toastr::success(__('messages.user_course_added'), __('status.success'));

        return redirect()->back();
    }

    // store user courses
    public function store2(CourseUsersRequest $request, Course $course)
    {
        $data = $request->validated();

        $this->userCoursesService->storeCourseUser($data, $course);
        Toastr::success(__('messages.user_course_added'), __('status.success'));

        return redirect()->back();
    }

    // change status
    public function changeStatus(Request $request, User $user, Course $course)
    {
        $data = $request->validate([
            'status' => 'required',
        ]);

        $this->userCoursesService->changeUserCourseStatus($data, $user, $course);

        Toastr::success(__('messages.user_course_status_updated'), __('status.success'));
        return redirect()->back();
    }
}
