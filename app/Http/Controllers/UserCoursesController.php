<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\UserCoursesRequest;
use App\Models\Course;
use App\Models\User;
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
    }

    // get user courses
    public function index(User $user)
    {
        $user_courses = $user->courses()->get();
        $courses = $this->userCoursesService->getCourses();
        $title = __('attributes.courses');
        return view('admin.user.courses', compact('user', 'user_courses', 'title', 'courses'));
    }

    // store user courses
    public function store(UserCoursesRequest $request, User $user)
    {
        $data = $request->validated();

        $this->userCoursesService->storeUserCourse($data, $user);
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
