<?php
// app/Services/StudentWorkCategoryService.php

namespace App\Services;


use App\Models\StudentWorkCategory;
use Illuminate\Database\Eloquent\Collection;

class StudentWorkCategoryService
{
    public function getStudentWorkCategories(): Collection
    {
        return StudentWorkCategory::all();
    }

    public function createStudentWorkCategory(array $data): StudentWorkCategory
    {
        $studentWorkCategory = StudentWorkCategory::create($data);

        return $studentWorkCategory;
    }

    public function updateStudentWorkCategory(StudentWorkCategory $studentWorkCategory, array $data): bool
    {
        $studentWorkCategory->update($data);

        return $studentWorkCategory->wasChanged();
    }

    public function deleteStudentWorkCategory(StudentWorkCategory $studentWorkCategory): bool
    {
        $result = $studentWorkCategory->delete();


        return $result;
    }
}
// app/Http/Controllers/Admin/StudentWorkCategoriesController.php
