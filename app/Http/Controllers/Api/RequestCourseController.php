<?php

namespace App\Http\Controllers\Api;

use App\Events\CourseRequestEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\RequestCourseRequest;
use App\Http\Resources\RequestCourseResource;
use App\Services\RequestCourseService;

class RequestCourseController extends Controller
{
    use ApiResponseTrait;

    private $requestCourseService;

    public function __construct(RequestCourseService $requestCourseService)
    {
        $this->requestCourseService = $requestCourseService;
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
