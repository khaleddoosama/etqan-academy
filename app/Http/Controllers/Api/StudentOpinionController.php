<?php

namespace App\Http\Controllers\Api;

use App\Events\CreateStudentOpinionEventEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StudentOpinionRequest;
use App\Http\Resources\StudentOpinionResource;
use App\Services\StudentOpinionService;

class StudentOpinionController extends Controller
{

    use ApiResponseTrait;

    protected $studentOpinionService;

    public function __construct(StudentOpinionService $studentOpinionService)
    {
        $this->studentOpinionService = $studentOpinionService;
    }

    public function index()
    {
        $items = $this->studentOpinionService->getForTheWholeSystem();
        return $this->apiResponse(StudentOpinionResource::collection($items), 'ok', 200);
    }

    public function store(StudentOpinionRequest $request)
    {
        $item = $this->studentOpinionService->store($request->validated());

        event(new CreateStudentOpinionEventEvent([], ['userName' => $item->student->name]));

        return $this->apiResponse(new StudentOpinionResource($item), __('messages.student_opinion_created_successfully'), 201);
    }
}
