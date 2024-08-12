<?php
// app/Services/RequestCourseService.php

namespace App\Services;


use App\Models\RequestCourse;
use Illuminate\Database\Eloquent\Collection;
use Yoeunes\Toastr\Facades\Toastr;

class RequestCourseService
{

    public function createRequestCourse(array $data): RequestCourse
    {
        return RequestCourse::create($data);
    }

    public function getRequestCourses(): Collection
    {
        return RequestCourse::all();
    }

    public function getRequestCourse($id): RequestCourse
    {
        return RequestCourse::findOrfail($id);
    }

    public function reply($id)
    {
        $inquiry = RequestCourse::findOrfail($id);
        $inquiry->status = 1;
        $inquiry->save();
        return true;
    }
}
