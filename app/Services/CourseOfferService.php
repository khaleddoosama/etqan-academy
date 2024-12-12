<?php

namespace App\Services;

use App\Models\CourseOffer;
use Illuminate\Database\Eloquent\Collection;

class CourseOfferService
{
    public function getAll(): Collection
    {
        return CourseOffer::all();
    }

    public function createCourseOffer(array $data): CourseOffer
    {
        $courseOffer = CourseOffer::create($data);

        return $courseOffer;
    }

    public function updateCourseOffer(CourseOffer $courseOffer, array $data): bool
    {
        $courseOffer->update($data);

        return $courseOffer->wasChanged();
    }

    public function deleteCourseOffer(CourseOffer $courseOffer): bool
    {
        $result = $courseOffer->delete();

        return $result;
    }
}
// app/Http/Controllers/Admin/CourseOfferController.php
