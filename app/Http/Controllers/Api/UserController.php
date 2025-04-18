<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserCoursesResource;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use ApiResponseTrait;
    protected $userService;
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }


    public function UserCourses()
    {
        $user_id = auth()->user()->id;
        $userCourses = UserCoursesResource::collection($this->userService->getActiveUserCourses($user_id));
        return $this->apiResponse($userCourses, 'ok', 200);
    }
}
