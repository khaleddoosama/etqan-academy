<?php

namespace App\Services;

use App\Models\Course;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class UserCoursesService
{
    protected $courseService;
    protected $userService;
    // constructor for CourseService
    public function __construct(CourseService $courseService, UserService $userService)
    {
        $this->courseService = $courseService;
        $this->userService = $userService;
    }

    // add user course
    public function storeUserCourse(array $data, User $user)
    {
        return $user->courses()->attach($data['course_id']);
    }

    // add course user
    public function storeCourseUser(array $data, Course $course)
    {
        return $course->students()->attach($data['user_id']);
    }

    // get courses
    public function getCourses()
    {
        return $this->courseService->getCourses();
    }

    // get students
    public function getStudents()
    {
        return $this->userService->getStudents();
    }



    // change user course status
    public function changeUserCourseStatus(array $data, User $user, Course $course)
    {
        $course = $user->courses()->where('course_id', $course->id)->first();
        
        return $course->pivot->update(['status' => $data['status']]);
    }
}
