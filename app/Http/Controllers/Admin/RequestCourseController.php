<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Notifications\StudentApprovedNotification;
use App\Services\RequestCourseService;
use App\Services\StudentsNotificationService;
use Illuminate\Http\Request;
use Yoeunes\Toastr\Facades\Toastr;

class RequestCourseController extends Controller
{
    private $requestCourseService;
    private $studentsNotificationService;


    public function __construct(RequestCourseService $requestCourseService, StudentsNotificationService $studentsNotificationService)
    {
        $this->requestCourseService = $requestCourseService;
        $this->studentsNotificationService = $studentsNotificationService;

        $this->middleware('permission:request_course.list')->only('index');
        $this->middleware('permission:request_course.show')->only('show');
        $this->middleware('permission:request_course.status')->only('reply');
    }

    // index
    public function index()
    {
        $requestCourses = $this->requestCourseService->getRequestCourses();
        return view('admin.request_course.index', compact('requestCourses'));
    }

    public function show($id)
    {
        $requestCourse = $this->requestCourseService->getRequestCourse($id);
        return view('admin.request_course.show', compact('requestCourse'));
    }

    public function status(Request $request, $id)
    {
        $request_course = $this->requestCourseService->changeStatus($request->status, $id);
        if ($request_course->status == 1) {
            // send email
            $notification = new StudentApprovedNotification($request_course->course->slug, $request_course->course->title);
            $this->studentsNotificationService->notify($notification, $request_course->student);
        }


        Toastr::success(__('messages.requestCourse_changed'), __('status.success'));

        return redirect()->route('admin.request_courses.index');
    }
}
