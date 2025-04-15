<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\StudentOpinionService;
use Illuminate\Http\Request;
use Yoeunes\Toastr\Facades\Toastr;

class StudentOpinionController extends Controller
{
    public $studentOpinionService;
    public function __construct(StudentOpinionService $studentOpinionService)
    {
        $this->studentOpinionService = $studentOpinionService;
    }

    public function index()
    {
        $studentOpinions = $this->studentOpinionService->all();
        return view('admin.student_opinion.index', compact('studentOpinions'));
    }

    public function status($studentOpinionId, Request $request)
    {
        $request->validate([
            'status' => 'required|in:1,2',
        ]);
        $studentOpinion = $this->studentOpinionService->changeStatus($studentOpinionId, $request->status);
        Toastr::success(__('messages.student_opinion_status_updated'), __('status.success'));
        return redirect()->back();
    }
}
