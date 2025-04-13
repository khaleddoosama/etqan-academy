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
    protected $progressService;
    protected $sectionService;

    public function __construct(ProgressService $progressService, SectionService $sectionService)
    {
        $this->progressService = $progressService;
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
        $data['disk'] = 'public';
        $data['position'] = Lecture::where('section_id', $data['section_id'])->max('position') + 1;
        $data['processed'] = 1;
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
        // $newLecture->processed = 0; 
        // Save the new lecture
        $newLecture->save();

        return [$lecture, $newLecture];
    }

    // increase views
    public function increaseViews(Lecture $lecture)
    {
        $lectureView = LectureViews::firstOrCreate(
            ['user_id' => auth('api')->id(), 'lecture_id' => $lecture->id],
            ['views' => 0]
        );
        $lectureView->increment('views');
        $count = $lectureView->lecture_views_count;
        $course = $lecture->course;
        $this->progressService->updateProgress(auth('api')->id(), $course->id, $count, $course->countLectures());

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

    // get lectures based on section
    public function getLectures(int $section_id)
    {
        return Lecture::where('section_id', $section_id)->get();
    }

    public function getAllLectures()
    {
        return Lecture::with(['section', 'section.course'])->get();
    }

    public function updateVideoPaths($lectureId, $videoPaths)
    {
        $lecture = Lecture::find($lectureId);

        if (!$lecture) {
            return false;
        }

        foreach ($videoPaths as $format => $path) {
            if ($path) {
                $lecture->convertedVideo[$format] = $path;
            } else {
                $lecture->convertedVideo[$format] = null;
            }
        }
        return $lecture->convertedVideo->save();
    }

    // getFailedLectures
    public function getFailedLectures()
    {
        $lectures = Lecture::where('processed', 0)->orWhereHas('convertedVideo', function ($query) {
            $query->whereNull('mp4_Format_720')->whereNull('mp4_Format_1080');
        })->get();

        return $lectures;
    }
}
