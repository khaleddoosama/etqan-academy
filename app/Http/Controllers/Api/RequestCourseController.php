<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\RequestCourseRequest;
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

        $requestCourse = $this->requestCourseService->createRequestCourse($data);

        $notification = new CourseRequestNotification($requestCourse->student->name, $requestCourse->id);
        $this->adminNotificationService->notifyAdmins($notification);

        return $this->apiResponse($requestCourse, 'Request sent successfully', 201);
    }
}
