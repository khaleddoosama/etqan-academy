<?php
// app/Services/CourseService.php

namespace App\Services;


use App\Models\Course;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CourseService
{
    public function getCourse(string $id): Course
    {
        return Course::findOrFail($id);
    }
    public function getCourseBySlug(string $slug)
    {
        return Course::where('slug', $slug)->first();
    }

    public function getCourses(): Collection
    {
        return Cache::remember('courses', 60, function () {
            return Course::all();
        });
    }
    public function getActiveCourses(): Collection
    {
        return Cache::remember('active_courses', 60, function () {
            return Course::active()->get();
        });
    }

    public function createCourse(array $data): Course
    {
        DB::beginTransaction();
        try {
            $courseData = Arr::except($data, ['sections']); // Remove 'sections' from the data array
            $course = Course::create($courseData); // Create the course without the 'sections' data

            if (isset($data['sections'])) {
                foreach ($data['sections'] as $section) {
                    $course->sections()->create($section);
                }
            }

            // Clear cache after creating a new course
            Cache::forget('courses');

            DB::commit();
            return $course;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }




    public function updateCourse(Course $course, array $data): bool
    {
        // $data['slug'] = str_replace(' ', '-', $data['title']);
        $courseData = Arr::except($data, ['sections']); // Remove 'sections' from the data array
        $course->update($courseData);

        if (isset($data['sections'])) {
            // Convert incoming sections to a collection for easier manipulation
            $incomingSections = collect($data['sections']);

            // Get current sections from the database
            $currentSections = $course->sections()->get();

            // Update and add sections
            foreach ($incomingSections as $sectionData) {

                $section = $currentSections->where('id', $sectionData['id'] ?? 0)->first();
                if ($section) {
                    // If section exists, update it if necessary
                    $section->update($sectionData);
                } else {
                    // If section does not exist, create it
                    $course->sections()->create($sectionData);
                }
            }

            // Find and remove any sections that are no longer present
            $incomingSectionIds = $incomingSections->pluck('id');
            $sectionsToRemove = $currentSections->whereNotIn('id', $incomingSectionIds);
            foreach ($sectionsToRemove as $sectionToRemove) {
                $sectionToRemove->delete();
            }
        }

        // Clear cache after updating a course
        Cache::forget('courses');

        return $course->wasChanged();
    }
    public function deleteCourse(Course $course): bool
    {
        DB::beginTransaction();
        try {
            // first delete lectures
            $sections = $course->sections()->with('lectures')->get();
            $lectures = $sections->pluck('lectures')->flatten();

            foreach ($lectures as $lecture) {
                $lectureService = app(LectureService::class);
                $lectureService->deleteLecture($lecture);
            }

            // second delete sections
            $course->sections()->delete();

            // Clear cache after deleting a course
            Cache::forget('courses');
            Cache::forget('course_' . $course->id);
            $cacheKey = "sections_course_{$course->slug}";
            Cache::forget($cacheKey);

            $result = $course->delete();

            DB::commit();


            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    // change status
    public function changeStatus($status, $id): bool
    {
        $course = $this->getCourse($id);

        $course->update(['status' => $status]);

        // Clear cache after updating a course
        Cache::forget('courses');
        return $course->wasChanged();
    }
}
