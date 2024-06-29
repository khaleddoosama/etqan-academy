<?php

namespace App\Services;


use App\Models\Instructor;
use Illuminate\Database\Eloquent\Collection;
use Yoeunes\Toastr\Facades\Toastr;

class InstructorService
{
    public function getInstructors(): Collection
    {
        return Instructor::all();
    }

    public function createInstructor(array $data): Instructor
    {
        return Instructor::create($data);
    }

    public function updateInstructor(Instructor $instructor, array $data): bool
    {
        // $data['slug'] = str_replace(' ', '-', $data['name']);
        $instructor->update($data);

        return $instructor->wasChanged();
    }

    public function deleteInstructor(Instructor $instructor): bool
    {
        return $instructor->delete();
    }
}
// app/Http/Controllers/Admin/InstructorController.php
