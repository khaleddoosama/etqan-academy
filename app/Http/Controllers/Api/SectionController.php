<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SectionResource;
use App\Services\SectionService;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    use ApiResponseTrait;
    protected SectionService $sectionService;

    public function __construct(SectionService $sectionService)
    {
        $this->sectionService = $sectionService;
    }

    public function index($course_slug)
    {
        $sections = SectionResource::collection($this->sectionService->getSectionsByCourseSlug($course_slug));
        if ($sections && count($sections) > 0) {
            return $this->apiResponse($sections, 'ok', 200);
        } else {
            return $this->apiResponse(null, 'sections not found', 404);
        }
    }

    public function show($course_slug, $section_slug)
    {
        $section = $this->sectionService->getSectionByCourseSlugAndSlug($course_slug, $section_slug);

        if ($section) {
            return $this->apiResponse(new SectionResource($section), 'ok', 200);
        }

        return $this->apiResponse(null, 'section not found', 404);
    }
}
