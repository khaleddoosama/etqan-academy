<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StudentWorkResource;
use App\Services\StudentWorkService;
use Illuminate\Http\Request;

class StudentWorkController extends Controller
{
    use ApiResponseTrait;

    protected $studentWorkService;

    public function __construct(StudentWorkService $studentWorkService)
    {
        $this->studentWorkService = $studentWorkService;
    }

    public function index()
    {
        $studentWorks = $this->studentWorkService->getStudentWorksGroupedByCategoryAndType();
        // dd($studentWorks);
        return $this->apiResponse(StudentWorkResource::collection($studentWorks), 'ok', 200);
    }
}
