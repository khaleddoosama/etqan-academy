<?php
// app/Services/SectionService.php

namespace App\Services;


use App\Models\Section;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

class SectionService
{
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
            })->get();
        });
    }
}
