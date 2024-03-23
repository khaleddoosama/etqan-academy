<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LectureRequest;
use App\Models\Lecture;
use App\Services\LectureService;
use Illuminate\Http\Request;
use Yoeunes\Toastr\Facades\Toastr;

class LectureController extends Controller
{

    protected $lectureService;
    public function __construct(LectureService $lectureService)
    {
        $this->lectureService = $lectureService;
    }

    public function index()
    {
        //
    }


    public function create()
    {
        //
    }


    public function store(LectureRequest $request)
    {
        $data = $request->validated();

        $lecture = $this->lectureService->createLecture($data);

        Toastr::success(__('messages.lecture_created'), __('status.success'));

        return redirect()->back();
    }


    public function show(string $id)
    {
        //
    }


    public function edit(string $id)
    {
        //
    }


    public function update(LectureRequest $request, Lecture $lecture)
    {
        $data = $request->validated();

        $this->lectureService->updateLecture($lecture, $data) ? Toastr::success(__('messages.lecture_updated'), __('status.success')) : '';

        return redirect()->back();
    }
    public function destroy(string $id)
    {
        //
    }
}
