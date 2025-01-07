<?php

namespace App\Services;

use App\Models\Course;
use App\Models\User;
use App\Models\UserCourse;

class UserCoursesService
{
    //create user course
    public function createUserCourse($student_id, $course_id)
    {
        return UserCourse::updateOrCreate(['student_id' => $student_id, 'course_id' => $course_id], ['status' => 1]);
    }


    // change user course status
    public function changeUserCourseStatus(array $data, User $student, Course $course)
    {
        $course = $student->courses()->where('course_id', $course->id)->first();

        if (!$course) {
            return false;
        }

        return $course->pivot->update(['status' => $data['status']]);
    }
}
