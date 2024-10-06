<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\RequestCourseRequest;
use App\Http\Resources\RequestCourseResource;
use App\Notifications\CourseRequestNotification;
use App\Services\AdminNotificationService;
use App\Services\RequestCourseService;

class RequestCourseController extends Controller
{
    use ApiResponseTrait;

    private $requestCourseService;
    private $adminNotificationService;

    public function __construct(RequestCourseService $requestCourseService, AdminNotificationService $adminNotificationService)
    {
        $this->requestCourseService = $requestCourseService;
        $this->adminNotificationService = $adminNotificationService;
    }

    // store
    public function store(RequestCourseRequest $request)
    {
        $data = $request->validated();

        $requestCourse = new RequestCourseResource($this->requestCourseService->createRequestCourse($data));

        $notification = new CourseRequestNotification($requestCourse->student->name ?? 'Guest', $requestCourse->id);
        $this->adminNotificationService->notifyAdmins($notification, permissions: ['request_course.list', 'request_course.show']);

        return $this->apiResponse($requestCourse, __('messages.requestCourse_sent'), 201);
    }
}
