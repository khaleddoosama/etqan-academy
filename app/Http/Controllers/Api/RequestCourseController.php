<?php

namespace App\Http\Controllers\Api;

use App\Events\CourseRequestEvent;
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

        event(new CourseRequestEvent([],
        [
            'student_name' => $requestCourse->student->name ?? 'Guest',
            'course_request_id' => $requestCourse->id
        ]));



        return $this->apiResponse($requestCourse, __('messages.requestCourse_sent'), 201);
    }
}
