<?php
// app/Services/SectionService.php

namespace App\Services;


use App\Models\Section;
use App\Traits\DuplicatorTrait;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Support\Facades\DB;

class SectionService
{
    use DuplicatorTrait;

    private $lectureService;
    public function __construct(LectureService $lectureService)
    {
        $this->lectureService = $lectureService;
    }

    public function getSection(string $id): Section
    {
        return Section::findOrFail($id);
    }
    public function getSectionByCourseSlugAndSlug(string $courseSlug, string $slug)
    {
        $cacheKey = "section_{$courseSlug}_{$slug}";
        return Cache::remember($cacheKey, 60, function () use ($courseSlug, $slug) {
            $section = Section::where('slug', $slug)->first();
            if ($section && $section->course->slug === $courseSlug) {
                return $section;
            } else {
                return null;
            }
        });
    }

    public function getSectionsByCourseSlug(string $courseSlug): Collection
    {
        $cacheKey = "sections_course_{$courseSlug}";
        return Cache::remember($cacheKey, 60, function () use ($courseSlug) {
            return Section::whereHas('course', function ($query) use ($courseSlug) {
                $query->where('slug', $courseSlug);
            })->with('course')->get();
        });
    }

    //getSectionsByCourseId
    public function getSectionsByCourseId($course_id)
    {
        return Section::where('course_id', $course_id)->get(['id', 'title']);
    }

    public function duplicateSection(int $sectionId, int $courseId): array
    {
        try {
            DB::beginTransaction();
            $section = Section::with('lectures')->findOrFail($sectionId);

            $newSection = $this->duplicateModelWithSlug($section, 'slug', [
                'course_id' => $courseId,
            ]);

            foreach ($section->lectures as $lecture) {
                $this->lectureService->duplicateLecture($lecture->id, $newSection->id);
            }

            DB::commit();
            return [$section, $newSection];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
