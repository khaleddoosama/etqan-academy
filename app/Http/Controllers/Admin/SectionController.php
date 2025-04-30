<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Api\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Services\CourseService;
use App\Services\SectionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yoeunes\Toastr\Facades\Toastr;

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

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'course_id' => 'required|exists:courses,id',
            'parent_section_id' => 'nullable|exists:sections,id',
            'description' => 'nullable|string',
        ]);

        $section = $this->sectionService->createSection($data);

        Toastr::success(__('messages.section_created_successfully'), __('status.success'));

        return redirect()->back();
    }



    public function show(Section $section)
    {
        $section->load(['lectures', 'parentSection', 'childrenSections.lectures']);

        $courses = $this->courseService->getCourses();
        return view('admin.section.show', compact('section', 'courses'));
    }

    // get section based on course
    public function getSections($course_id)
    {
        $sections = $this->sectionService->getSectionsByCourseId($course_id);

        return $this->apiResponse($sections, 'ok', 200);
    }

    public function duplicate(Request $request)
    {
        [$section, $newSection] = $this->sectionService->duplicateSection($request->section_id, $request->course_id, $request->parent_section_id);


        Toastr::success(__('messages.section_duplicated'), __('status.success'));

        return redirect()->back();
    }

    public function destroy(Section $section)
    {
        $section->delete();

        Toastr::success(__('messages.section_deleted_successfully'), __('status.success'));

        return redirect()->route('admin.courses.index');
    }

    public function reassignAndSort(Request $request)
    {
        $sectionId = $request->input('section_id');
        $newParentId = $request->input('new_parent_id'); // ممكن يكون null
        $newOrder = $request->input('new_order');

        DB::transaction(function () use ($sectionId, $newParentId, $newOrder) {
            Section::where('id', $sectionId)->update([
                'parent_section_id' => $newParentId
            ]);

            foreach ($newOrder as $index => $id) {
                Section::where('id', $id)->update(['position' => $index + 1]);
            }
        });

        return response()->json(['status' => 'success']);
    }

    public function update(Request $request, Section $section)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'parent_section_id' => 'nullable|exists:sections,id',
        ]);

        $section->update($data);

        Toastr::success(__('messages.section_updated_successfully'), __('status.success'));

        return redirect()->back();
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->ids ?? [];

        Section::whereIn('id', $ids)->delete();

        return $this->apiResponse(null, 'ok', 200);
    }
}
