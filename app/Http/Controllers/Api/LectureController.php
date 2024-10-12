<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LectureResource;
use App\Services\LectureService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

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
        $lectures = LectureResource::collection($this->lectureService->getLecturesByCourseSlugAndSectionSlug($course_slug, $section_slug), false);

        if ($lectures && count($lectures) > 0) {
            return $this->apiResponse($lectures, 'ok', 200);
        } else {
            return $this->apiResponse(null, __('messages.lectures_not_found'), 404);
        }
    }

    public function show($course_slug, $section_slug, $lecture_slug)
    {
        // DB::beginTransaction();
        try {
            $lecture = $this->lectureService->getLectureByCourseSlugAndSectionSlugAndSlug($course_slug, $section_slug, $lecture_slug);

            if (Gate::denies('view', $lecture->course) && $lecture->is_free === 0) {
                return $this->apiResponse(null, __('messages.course_access_denied'), 403);
            }

            if ($lecture) {
                // $this->lectureService->increaseViews($lecture);
                return $this->apiResponse(new LectureResource($lecture), 'ok', 200);
            }

            // DB::commit();
            return $this->apiResponse(null, __('messages.lecture_not_found'), 404);
        } catch (\Exception $e) {
            // DB::rollBack();
            return $this->apiResponse(null, $e->getMessage(), 500);
        }
    }
}
