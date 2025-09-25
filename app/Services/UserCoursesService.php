<?php

namespace App\Services;

use App\Models\Course;
use App\Models\User;
use App\Models\UserCourse;

class UserCoursesService
{
    //create user course
    public function createUserCourse($student_id, $course_id, $expires_at = null)
    {
        return UserCourse::updateOrCreate(
            ['student_id' => $student_id, 'course_id' => $course_id],
            ['status' => 1, 'expires_at' => $expires_at]
        );
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

    // check if user and course not found in user course table
    public function checkUserAndCourse($course_id, $student_id): bool
    {
        $is_purchased = UserCourse::purchased($course_id, $student_id)->exists();

        return $is_purchased;
    }

    // set or clear expiry for a user course
    public function setCourseExpiry(int $student_id, int $course_id, ?\Carbon\Carbon $expires_at): bool
    {
        $record = UserCourse::where('student_id', $student_id)->where('course_id', $course_id)->first();
        if (!$record) {
            return false;
        }

        return $record->update([
            'expires_at' => $expires_at,
            'status' => 1,
        ]);
    }
}
