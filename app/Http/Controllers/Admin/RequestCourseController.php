<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\RequestCourseService;
use Illuminate\Http\Request;
use Yoeunes\Toastr\Facades\Toastr;

class RequestCourseController extends Controller
{
    private $requestCourseService;

    public function __construct(RequestCourseService $requestCourseService)
    {
        $this->requestCourseService = $requestCourseService;

        $this->middleware('permission:request_course.list')->only('index');
        $this->middleware('permission:request_course.show')->only('show');
        $this->middleware('permission:request_course.reply')->only('reply');
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

    public function reply($id)
    {
        $this->requestCourseService->reply($id);

        Toastr::success(__('messages.requestCourse_replied'), __('status.success'));

        return redirect()->route('admin.request_courses.index');
    }
}
