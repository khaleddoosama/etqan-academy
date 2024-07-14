<?php

namespace App\Services;

use App\Models\Course;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class UserCoursesService
{
    protected $courseService;
    // constructor for CourseService
    public function __construct(CourseService $courseService)
    {
        $this->courseService = $courseService;
    }

    // add user course
    public function storeUserCourse(array $data, User $user)
    {
        return $user->courses()->attach($data['course_id']);
    }

    // get courses
    public function getCourses()
    {
        return $this->courseService->getCourses();
    }

    // change user course status
    public function changeUserCourseStatus(array $data, User $user, Course $course)
    {
        $course = $user->courses()->where('course_id', $course->id)->first();

        return $course->pivot->update(['status' => $data['status']]);
    }
}
