<?php
// app/Services/StudentWorkService.php

namespace App\Services;


use App\Models\StudentWork;
use Illuminate\Database\Eloquent\Collection;

class StudentWorkService
{
    public function getStudentWorks(): Collection
    {
        return StudentWork::with('studentWorkCategory')->get();
    }

    public function getStudentWorksGroupedByCategoryAndType(): Collection
    {
        return StudentWork::with('studentWorkCategory')->get();
    }


    public function getStudentWork(int $id): StudentWork
    {
        return StudentWork::findOrFail($id);
    }

    public function createStudentWork(array $data): StudentWork
    {
        foreach ($data['pathes'] as $file) {
            $data['path'] = $file;

            $studentWork = StudentWork::create($data);
        }

        return $studentWork;
    }

    public function deleteStudentWork($id): bool
    {
        $studentWork = $this->getStudentWork($id);
        $result = $studentWork->delete();

        return $result;
    }

    public function updateOrder(array $studentWorks): void
    {
        foreach ($studentWorks as $order => $id) {
            $studentWork = $this->getStudentWork($id);
            $studentWork->position = $order;
            $studentWork->save();
        }
    }
}
// app/Http/Controllers/Admin/StudentWorkController.php
