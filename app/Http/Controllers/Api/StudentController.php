<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Services\StudentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StudentController extends Controller
{
    use ApiResponseTrait;
    private $studentService;

    public function __construct(StudentService $studentService)
    {
        $this->studentService = $studentService;
    }

    public function search(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2'
        ]);
        $students = $this->studentService->searchStudents($request->query('q', ''));
        return $this->apiResponse($students, __('messages.students_found'), 200);
    }

    public function showProfile($slug)
    {
        $student = $this->studentService->getStudentProfile($slug);

        if (!$student) {
            return $this->apiResponse(null, __('messages.student_not_found'), 404);
        } else {
            return $this->apiResponse(new UserResource($student), 'ok', 200);
        }
    }
}
