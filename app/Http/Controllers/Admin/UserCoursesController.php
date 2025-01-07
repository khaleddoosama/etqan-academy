<?php

namespace App\Http\Controllers\Admin;

use App\Events\StudentApprovedAtCourseEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CourseUsersRequest;
use App\Http\Requests\Admin\UserCoursesRequest;
use App\Models\Course;
use App\Models\User;
use App\Models\UserCourse;
use App\Notifications\StudentApprovedNotification;
use App\Services\CourseService;
use App\Services\UserCoursesService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Yoeunes\Toastr\Facades\Toastr;

class UserCoursesController extends Controller
{
    private UserCoursesService $userCoursesService;
    private CourseService $courseService;
    private UserService $userService;

    public function __construct(UserCoursesService $userCoursesService, CourseService $courseService, UserService $userService)
    {
        $this->userCoursesService = $userCoursesService;
        $this->courseService = $courseService;
        $this->userService = $userService;

        $this->middleware('permission:user_course.list')->only('index', 'showStudents');
        $this->middleware('permission:user_course.create')->only('store', 'store2');
        $this->middleware('permission:user_course.status')->only('changeStatus');
    }

    // get user courses
    public function index(User $user)
    {
        // $user_courses = $user->courses()->get();
        $user_courses = UserCourse::where('student_id', $user->id)->with('course')->get();
        $courses = $this->courseService->getCourses();
        $title = __('attributes.courses');
        return view('admin.user.courses', compact('user', 'user_courses', 'title', 'courses'));
    }

    public function showStudents(Course $course)
    {
        $course_students = UserCourse::where('course_id', $course->id)->with('student')->get();
        $title = __('attributes.students');
        $students = $this->userService->getStudents();

        return view('admin.course.show-students', compact('course_students', 'course', 'title', 'students'));
    }

    // store user courses
    public function storeByUser(UserCoursesRequest $request, User $user): \Illuminate\Http\RedirectResponse
    {
        $data = $request->validated();

        $this->userCoursesService->createUserCourse($user->id, $data['course_id']);
        $course = $this->courseService->getCourse($data['course_id']);
        // send email
        event(new StudentApprovedAtCourseEvent([$user->id], ['courseSlug' => $course->slug, 'course_title' => $course->title]));

        Toastr::success(__('messages.user_course_added'), __('status.success'));

        return redirect()->back();
    }

    // store user courses
    public function storeByCourse(CourseUsersRequest $request, Course $course): \Illuminate\Http\RedirectResponse
    {
        $data = $request->validated();

        $this->userCoursesService->createUserCourse($data['user_id'], $course->id);

        $user = $this->userService->getUser($data['user_id']);
        // send email
        event(new StudentApprovedAtCourseEvent([$data['user_id']], ['courseSlug' => $course->slug, 'course_title' => $course->title]));

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

        if ($data['status'] == 1) {
            // send email
            event(new StudentApprovedAtCourseEvent([$user->id], ['courseSlug' => $course->slug, 'course_title' => $course->title]));
        }

        Toastr::success(__('messages.user_course_status_updated'), __('status.success'));
        return redirect()->back();
    }
}
