<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Api\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Services\CourseService;
use App\Services\SectionService;

class SectionController extends Controller
{
    use ApiResponseTrait;

    protected $sectionService;
    protected $courseService;

    public function __construct(SectionService $sectionService, CourseService $courseService)
    {
        $this->sectionService = $sectionService;
        $this->courseService = $courseService;

        $this->middleware('permission:course.show')->only('show');
    }


    public function show(Section $section)
    {
        $section->with('lectures');

        $courses = $this->courseService->getCourses();
        return view('admin.section.show', compact('section', 'courses'));
    }

    // get section based on course
    public function getSections($course_id)
    {
        $sections = $this->sectionService->getSectionsByCourseId($course_id);

        return $this->apiResponse($sections, 'ok', 200);
    }
}
