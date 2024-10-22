<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CourseResource;
use App\Services\CourseService;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    use ApiResponseTrait;
    protected CourseService $courseService;

    public function __construct(CourseService $courseService)
    {
        $this->courseService = $courseService;
    }

    public function index()
    {
        $courses = CourseResource::collection($this->courseService->getActiveCourses());
        return $this->apiResponse($courses, 'ok', 200);
    }

    public function show($course_slug)
    {
        $course = $this->courseService->getCourseBySlug($course_slug);

        if ($course) {
            return $this->apiResponse(new CourseResource($course), 'ok', 200);
        }

        return $this->apiResponse(null, __('messages.course_not_found'), 404);
    }
}
