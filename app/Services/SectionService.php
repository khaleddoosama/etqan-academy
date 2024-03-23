<?php
// app/Services/SectionService.php

namespace App\Services;


use App\Models\Section;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;

class SectionService
{
    public function getSection(string $id): Section
    {
        return Section::findOrFail($id);
    }
    public function getSectionByCourseSlugAndSlug(string $courseSlug, string $slug)
    {
        $section = Section::where('slug', $slug)->first();
        if ($section && $section->course->slug === $courseSlug) {
            return $section;
        } else {
            return null;
        }
    }


    public function getSections(): Collection
    {
        return Section::all();
    }

    public function getSectionsByCourseSlug(string $courseSlug): Collection
    {
        return Section::whereHas('course', function ($query) use ($courseSlug) {
            $query->where('slug', $courseSlug);
        })->get();
    }

    public function createSection(array $data): Section
    {
        return Section::create($data);
    }

    public function updateSection(Section $section, array $data): bool
    {
        $data['slug'] = str_replace(' ', '-', $data['name']);
        $section->update($data);

        return $section->wasChanged();
    }

    public function deleteSection(Section $section): bool
    {
        return $section->delete();
    }
}
