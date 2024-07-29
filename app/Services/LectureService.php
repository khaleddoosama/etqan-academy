<?php
// app/Services/LectureService.php

namespace App\Services;


use App\Models\Lecture;
use App\Models\LectureViews;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class LectureService
{
    protected $userCoursesService;


    public function __construct(UserCoursesService $userCoursesService)
    {
        $this->userCoursesService = $userCoursesService;
    }

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
        $data['disk'] = 's3';
        $lecture = Lecture::create($data); // Create the lecture without the 'lectures' data

        return $lecture;
    }

    public function updateLecture(Lecture $lecture, array $data)
    {
        $lecture->update($data);


        return $lecture;
    }

    public function deleteLecture(Lecture $lecture): bool
    {
        $convertedVideo = $lecture->convertedVideo;

        if ($convertedVideo) {
            Storage::delete($convertedVideo->mp4_Format_240);
            Storage::delete($convertedVideo->mp4_Format_360);
            Storage::delete($convertedVideo->mp4_Format_480);
            Storage::delete($convertedVideo->mp4_Format_720);
            Storage::delete($convertedVideo->mp4_Format_1080);
            Storage::delete($convertedVideo->webm_Format_240);
            Storage::delete($convertedVideo->webm_Format_360);
            Storage::delete($convertedVideo->webm_Format_480);
            Storage::delete($convertedVideo->webm_Format_720);
            Storage::delete($convertedVideo->webm_Format_1080);
        }
        if ($lecture->thumbnail) {
            Storage::delete($lecture->thumbnail);
        }

        if ($lecture->video) {
            Storage::disk($lecture->disk)->delete($lecture->video);
        }


        return $lecture->delete();
    }

    public function duplicateLecture(int $lectureId, int $sectionId)
    {
        $lecture = Lecture::find($lectureId);

        // Create a new lecture instance and copy the properties from the original lecture
        $newLecture = $lecture->replicate();
        $newLecture->section_id = $sectionId;
        $newLecture->slug = $newLecture->slug . '-' . $newLecture->id;
        $newLecture->processed = 0;
        // Save the new lecture
        $newLecture->save();

        return [$lecture, $newLecture];
    }

    // increase views
    public function increaseViews(Lecture $lecture)
    {
        $lectureView = LectureViews::firstOrCreate(
            ['user_id' => auth()->id(), 'lecture_id' => $lecture->id],
            ['views' => 0]
        );
        $lectureView->increment('views');
        $count = $lectureView->lecture_views_count;
        $this->userCoursesService->updateProgress($count, $lecture->course);

        return $lectureView;
    }
}
