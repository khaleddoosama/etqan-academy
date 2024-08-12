<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\RequestCourseRequest;
use App\Services\RequestCourseService;
use Illuminate\Http\Request;

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

        $requestCourse = $this->requestCourseService->createRequestCourse($data);

        return $this->apiResponse($requestCourse, 'Request sent successfully', 201);
    }
}
