<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LectureResource;
use App\Services\LectureService;
use Illuminate\Http\Request;

class LectureController extends Controller
{
    use ApiResponseTrait;
    protected LectureService $lectureService;

    public function __construct(LectureService $lectureService)
    {
        $this->lectureService = $lectureService;
    }

    public function index($course_slug, $section_slug)
    {
        $lectures = LectureResource::collection($this->lectureService->getLecturesByCourseSlugAndSectionSlug($course_slug, $section_slug));
        
        if ($lectures && count($lectures) > 0) {
            return $this->apiResponse($lectures, 'ok', 200);
        } else {
            return $this->apiResponse(null, 'lectures not found', 404);
        }
    }

    public function show($course_slug, $section_slug, $lecture_slug)
    {
        $lecture = $this->lectureService->getSectionByCourseSlugAndSectionSlugAndSlug($course_slug, $section_slug, $lecture_slug);

        if ($lecture) {
            return $this->apiResponse(new LectureResource($lecture), 'ok', 200);
        }

        return $this->apiResponse(null, 'lecture not found', 404);
    }
}
