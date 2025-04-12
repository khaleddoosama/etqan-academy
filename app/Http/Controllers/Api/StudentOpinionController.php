<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StudentOpinionRequest;
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
        return $this->apiResponse(new StudentOpinionResource($item), 'ok', 201);
    }
}
