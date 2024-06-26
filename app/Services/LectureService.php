<?php
// app/Services/LectureService.php

namespace App\Services;


use App\Models\Lecture;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;

class LectureService
{
    public function getLecture(string $id)
    {
        return Lecture::findOrFail($id);
    }
    public function getSectionByCourseSlugAndSectionSlugAndSlug(string $courseSlug, string $sectionSlug, string $slug)
    {
        $lecture = Lecture::where('slug', $slug)->first();
        if ($lecture && $lecture->section->slug === $sectionSlug && $lecture->section->course->slug === $courseSlug) {
            return $lecture;
        } else {
            return null;
        }
    }


    public function getLectures(): Collection
    {
        return Lecture::all();
    }

    public function getLecturesByCourseSlugAndSectionSlug(string $courseSlug, string $sectionSlug): Collection
    {
        return Lecture::whereHas('section', function ($query) use ($courseSlug, $sectionSlug) {
            $query->whereHas('course', function ($query) use ($courseSlug) {
                $query->where('slug', $courseSlug);
            })->where('slug', $sectionSlug);
        })->get();
    }

    public function createLecture(array $data): Lecture
    {
        $lecture = Lecture::create($data); // Create the lecture without the 'lectures' data

        return $lecture;
    }

    public function updateLecture(Lecture $lecture, array $data): bool
    {
        $lecture->update($data);


        return $lecture->wasChanged();
    }

    public function deleteLecture(Lecture $lecture): bool
    {
        return $lecture->delete();
    }

    public function duplicateLecture(int $lectureId, int $sectionId)
    {
        $lecture = Lecture::find($lectureId);

        // Create a new lecture instance and copy the properties from the original lecture
        $newLecture = $lecture->replicate();
        $newLecture->section_id = $sectionId;

        // Save the new lecture
        $newLecture->save();
        
        return $lecture;
    }
}
