<?php
// app/Services/LectureService.php

namespace App\Services;


use App\Models\Lecture;
use App\Models\LectureViews;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class LectureService
{
    protected $userCoursesService;
    protected $sectionService;

    public function __construct(UserCoursesService $userCoursesService, SectionService $sectionService)
    {
        $this->userCoursesService = $userCoursesService;
        $this->sectionService = $sectionService;
    }

    public function getLecture(string $id)
    {
        return Cache::remember("lecture_{$id}", 60, function () use ($id) {
            return Lecture::findOrFail($id);
        });
    }
    public function getLectureByCourseSlugAndSectionSlugAndSlug(string $courseSlug, string $sectionSlug, string $slug)
    {
        $lecture = Lecture::where('slug', $slug)->where('processed', '=', 1)->first();
        if ($lecture && $lecture->section->slug === $sectionSlug && $lecture->section->course->slug === $courseSlug) {
            return $lecture;
        } else {
            return null;
        }
    }

    public function getLecturesByCourseSlugAndSectionSlug(string $courseSlug, string $sectionSlug): Collection
    {
        $cacheKey = "lectures_{$courseSlug}_{$sectionSlug}";
        return Cache::remember($cacheKey, 60, function () use ($courseSlug, $sectionSlug) {
            return Lecture::where('processed', '=', 1)->whereHas('section', function ($query) use ($courseSlug, $sectionSlug) {
                $query->whereHas('course', function ($query) use ($courseSlug) {
                    $query->where('slug', $courseSlug);
                })->where('slug', $sectionSlug);
            })->get();
        });
    }

    public function createLecture(array $data): Lecture
    {
        $data['disk'] = 's3';
        $data['position'] = Lecture::where('section_id', $data['section_id'])->max('position') + 1;
        $lecture = Lecture::create($data); // Create the lecture without the 'lectures' data

        // Clear cache after creating a new lecture
        Cache::forget('lectures');

        return $lecture;
    }

    public function updateLecture(Lecture $lecture, array $data)
    {
        $lecture->update($data);

        // Clear cache after updating a lecture
        Cache::forget("lecture_{$lecture->id}");
        Cache::forget('lectures');

        return $lecture;
    }

    public function deleteLecture(Lecture $lecture): bool
    {
        $convertedVideo = $lecture->convertedVideo;
        if ($convertedVideo) {
            $convertedVideo->mp4_Format_240 !== null ? Storage::delete($convertedVideo->mp4_Format_240) : null;
            $convertedVideo->mp4_Format_360 !== null ? Storage::delete($convertedVideo->mp4_Format_360) : null;
            $convertedVideo->mp4_Format_480 !== null ? Storage::delete($convertedVideo->mp4_Format_480) : null;
            $convertedVideo->mp4_Format_720 !== null ? Storage::delete($convertedVideo->mp4_Format_720) : null;
            $convertedVideo->mp4_Format_1080 !== null ? Storage::delete($convertedVideo->mp4_Format_1080) : null;
            $convertedVideo->webm_Format_240 !== null ? Storage::delete($convertedVideo->webm_Format_240) : null;
            $convertedVideo->webm_Format_360 !== null ? Storage::delete($convertedVideo->webm_Format_360) : null;
            $convertedVideo->webm_Format_480 !== null ? Storage::delete($convertedVideo->webm_Format_480) : null;
            $convertedVideo->webm_Format_720 !== null ? Storage::delete($convertedVideo->webm_Format_720) : null;
            $convertedVideo->webm_Format_1080 !== null ? Storage::delete($convertedVideo->webm_Format_1080) : null;
        }
        if ($lecture->thumbnail) {
            Storage::delete($lecture->thumbnail);
        }

        if ($lecture->video) {
            Storage::disk($lecture->disk)->delete($lecture->video);
        }

        if ($lecture->attachments) {
            foreach ($lecture->attachments as $attachment) {
                Storage::disk($lecture->disk)->delete($attachment['path']);
            }
        }

        // Clear cache after deleting a lecture
        Cache::forget("lecture_{$lecture->id}");
        Cache::forget('lectures');

        return $lecture->delete();
    }

    public function duplicateLecture(int $lectureId, int $sectionId)
    {
        $lecture = Lecture::find($lectureId);

        // Create a new lecture instance and copy the properties from the original lecture
        $newLecture = $lecture->replicate();
        $newLecture->section_id = $sectionId;
        $newLecture->slug = SlugService::createSlug(Lecture::class, 'slug', $lecture->title);
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

    // get section by id
    public function getSection(int $id)
    {
        return $this->sectionService->getSection($id);
    }

    // get section by id
    public function getSections($course_slug = '')
    {
        return $this->sectionService->getSectionsByCourseSlug($course_slug); // Get all sections for all courses
    }

    // Update an attachment
    public function updateAttachment(Lecture $lecture, $attachment_path, $attachment_name)
    {
        $attachments = $this->getUpdatedAttachments($lecture, $attachment_path, function (&$attachment) use ($attachment_name) {
            $attachment['originalName'] = $attachment_name;
        });

        DB::table('lectures')->where('id', $lecture->id)->update(['attachments' => $attachments]);
        return $lecture;
    }

    // Delete an attachment
    public function deleteAttachment(Lecture $lecture, $attachment_path)
    {
        $attachments = $this->getUpdatedAttachments($lecture, $attachment_path, function (&$attachment) {
            Storage::delete($attachment['path']);
        }, true);

        DB::table('lectures')->where('id', $lecture->id)->update(['attachments' => $attachments]);
    }

    // Helper method to update attachments
    private function getUpdatedAttachments(Lecture $lecture, $attachment_path, $callback, $remove = false)
    {
        $attachments = $lecture->attachments;
        foreach ($attachments as $key => $value) {
            if ($value['path'] === $attachment_path) {
                $callback($attachments[$key]);
                if ($remove) {
                    unset($attachments[$key]);
                }
                break;
            }
        }

        return array_values($attachments);
    }

    // change is free status
    public function changeIsFree(Lecture $lecture)
    {
        $lecture->is_free = !$lecture->is_free;
        $lecture->save();
        return $lecture;
    }
}
