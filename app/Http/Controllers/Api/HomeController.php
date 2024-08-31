<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Lecture;
use App\Models\User;
use App\Models\UserCourse;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    use ApiResponseTrait;
    public function home()
    {
        $students_subscribers = User::student()->count() + 6953;
        $lectures = Lecture::where('processed', 1)->count() + 526;
        $students_graduates = UserCourse::select('student_id')->where('completed', 1)->groupBy('student_id')->get()->count() + 3320;
        return $this->apiResponse(compact('students_subscribers', 'lectures', 'students_graduates'), 'ok', 200);
    }
}
