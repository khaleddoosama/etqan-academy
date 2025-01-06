<?php

namespace App\Services;

use App\Models\UserCourse;

class ProgressService
{
    public function calculateProgress(int $completedLectures, int $totalLectures): float
    {
        return $totalLectures > 0 ? ($completedLectures / $totalLectures) * 100 : 0;
    }

    public function updateProgress(int $userId, int $courseId, int $completedLectures, int $totalLectures)
    {
        $progress = $this->calculateProgress($completedLectures, $totalLectures);

        $userCourse = UserCourse::where('student_id', $userId)
            ->where('course_id', $courseId)
            ->first();

        if (!$userCourse) {
            return false;
        }

        $userCourse->update([
            'progress' => $progress,
            'completed' => $progress === 100 ? 1 : 0,
        ]);

        return $userCourse;
    }
}
