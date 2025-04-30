<?php

namespace App\Services;


use App\Models\Program;
use Illuminate\Database\Eloquent\Collection;
use Yoeunes\Toastr\Facades\Toastr;

class ProgramService
{
    public function getAll(): Collection
    {
        return Program::all();
    }

    public function createProgram(array $data): Program
    {
        return Program::create($data);
    }

    public function updateProgram(Program $program, array $data): bool
    {
        // $data['slug'] = str_replace(' ', '-', $data['name']);
        $program->update($data);

        return $program->wasChanged();
    }

    public function deleteProgram(Program $program): bool
    {
        return $program->delete();
    }
}
// app/Http/Controllers/Admin/ProgramController.php
