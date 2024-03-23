<?php
// app/Services/CourseService.php

namespace App\Services;


use App\Models\Course;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;

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
        return Course::all();
    }

    public function createCourse(array $data): Course
    {
        $courseData = Arr::except($data, ['sections']); // Remove 'sections' from the data array
        $course = Course::create($courseData); // Create the course without the 'sections' data

        if (isset($data['sections'])) {
            foreach ($data['sections'] as $section) {
                $course->sections()->create($section);
            }
        }
        return $course;

        // return Course::create($data);
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
        return $course->wasChanged();
    }

    public function deleteCourse(Course $course): bool
    {
        return $course->delete();
    }
}
