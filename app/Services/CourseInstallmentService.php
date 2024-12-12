<?php

namespace App\Services;

use App\Models\CourseInstallment;
use Illuminate\Database\Eloquent\Collection;

class CourseInstallmentService
{
    public function getAll(): Collection
    {
        return CourseInstallment::all();
    }

    public function createCourseInstallment(array $data): CourseInstallment
    {
        $courseInstallment = CourseInstallment::create($data);

        return $courseInstallment;
    }

    public function updateCourseInstallment(CourseInstallment $courseInstallment, array $data): bool
    {
        $courseInstallment->update($data);

        return $courseInstallment->wasChanged();
    }

    public function deleteCourseInstallment(CourseInstallment $courseInstallment): bool
    {
        $result = $courseInstallment->delete();

        return $result;
    }
}
// app/Http/Controllers/Admin/CourseInstallmentController.php
