<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\InquiryService;
use Illuminate\Http\Request;
use Yoeunes\Toastr\Facades\Toastr;

class InquiryController extends Controller
{
    protected $inquiryService;
    public function __construct(InquiryService $inquiryService)
    {
        $this->inquiryService = $inquiryService;

        $this->middleware('permission:inquiry.list')->only('index');
        $this->middleware('permission:inquiry.show')->only('show');
        $this->middleware('permission:inquiry.reply')->only('reply');

    }
    public function index()
    {
        $inquiries = $this->inquiryService->getInquiries();
        return view('admin.inquiry.index', compact('inquiries'));
    }

    public function show($id)
    {
        $inquiry = $this->inquiryService->getInquiry($id);
        return view('admin.inquiry.show', compact('inquiry'));
    }

    public function reply($id)
    {
        $this->inquiryService->reply($id);

        Toastr::success(__('messages.inquiry_replied'), __('status.success'));

        return redirect()->route('admin.inquiries.index');
    }
}
