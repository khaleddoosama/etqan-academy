<?php
// app/Services/RequestCourseService.php

namespace App\Services;


use App\Models\RequestCourse;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class RequestCourseService
{

    protected UserCoursesService $userCoursesService;
    protected UserService $userService;
    protected CourseService $courseService;
    // constructor for CourseService
    public function __construct(UserCoursesService $userCoursesService, UserService $userService, CourseService $courseService)
    {
        $this->userCoursesService = $userCoursesService;
        $this->userService = $userService;
        $this->courseService = $courseService;
    }

    public function createRequestCourse(array $data): RequestCourse
    {
        $course = $this->courseService->getCourseBySlug($data['course_slug']);
        unset($data['course_slug']);
        $data['course_id'] = $course->id;
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
        if (!$request->student) {
            // search for user with phone
            $student = $this->userService->getStudentByPhone($request->phone);

            if (!$student) {
                // return error
                throw ValidationException::withMessages(['phone' => 'can not find student with this phone']);
            }

            $request->student_id = $student->id;
            $request->save();
        }

        if ($status == 1) {
            $this->userCoursesService->createUserCourse($request->student_id, $request->course_id);
            $request->approved_by = auth()->user()->id;
            $request->approved_at = now();
        } elseif ($status == 2) {
            $this->userCoursesService->changeUserCourseStatus(['status' => 0], $request->student ?? $student, $request->course);
            $request->rejected_by = auth()->user()->id;
            $request->rejected_at = now();
        }

        $request->status = $status;
        $request->save();
        return $request;
    }
}
