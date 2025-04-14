<?php
// app/Services/LectureService.php

namespace App\Services;


use App\Models\LectureViews;

class LectureViewService
{
    public function recordView(int $userId, $lecture_id): LectureViews
    {
        $view = LectureViews::firstOrCreate(
            ['user_id' => $userId, 'lecture_id' => $lecture_id],
            ['views' => 0]
        );

        $view->increment('views');

        return $view;
    }

    // get Course Views
    public function getViews(int $user_id,  $lectureIds)
    {
        return LectureViews::where('user_id', $user_id)
            ->whereIn('lecture_id', $lectureIds)
            ->distinct('lecture_id')
            ->count('lecture_id');
    }
}
