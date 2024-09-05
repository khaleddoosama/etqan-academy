<?php
// app/Services/RequestCourseService.php

namespace App\Services;


use App\Models\RequestCourse;
use Illuminate\Database\Eloquent\Collection;

class RequestCourseService
{

    protected $userCoursesService;
    // constructor for CourseService
    public function __construct(UserCoursesService $userCoursesService)
    {
        $this->userCoursesService = $userCoursesService;
    }

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

    // change status
    public function changeStatus($status, $id)
    {
        $request = $this->getRequestCourse($id);
        if ($status == 1) {
            $this->userCoursesService->createUserCourse($request->student_id, $request->course_id);
            $request->approved_by = auth()->user()->id;
            $request->approved_at = now();
        } elseif ($status == 2) {
            $this->userCoursesService->changeUserCourseStatus(['status' => 0], $request->student, $request->course);
            $request->rejected_by = auth()->user()->id;
            $request->rejected_at = now();
        }

        $request->status = $status;
        $request->save();
        return $request;
    }
}
