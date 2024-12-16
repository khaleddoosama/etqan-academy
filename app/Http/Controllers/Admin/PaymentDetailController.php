<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Mail\PaymentApprovedMail;
use App\Notifications\PaymentApprovedNotification;
use App\Notifications\PaymentRejectedNotification;
use App\Services\PaymentDetailService;
use App\Services\StudentsNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Yoeunes\Toastr\Facades\Toastr;

class PaymentDetailController extends Controller
{
    private $paymentDetailService;
    private $studentsNotificationService;


    public function __construct(PaymentDetailService $paymentDetailService, StudentsNotificationService $studentsNotificationService)
    {
        $this->paymentDetailService = $paymentDetailService;
        $this->studentsNotificationService = $studentsNotificationService;

        $this->middleware('permission:payment_detail.list')->only('index');
        $this->middleware('permission:payment_detail.show')->only('show');
        $this->middleware('permission:payment_detail.status')->only('reply');
    }

    // index
    public function index()
    {
        $paymentDetails = $this->paymentDetailService->getPaymentDetails();
        return view('admin.payment_detail.index', compact('paymentDetails'));
    }

    public function show($id)
    {
        $paymentDetail = $this->paymentDetailService->getPaymentDetail($id);
        return view('admin.payment_detail.show', compact('paymentDetail'));
    }

    public function updateAmount(Request $request, $id)
    {
        $data = $request->validate([
            'amount' => 'required|numeric',
        ]);

        $this->paymentDetailService->update($data, $id);

        Toastr::success(__('messages.amount_updated_successfully'), __('status.success'));

        return redirect()->back();
    }

    public function status(Request $request, $id)
    {
        $paymentDetail = $this->paymentDetailService->changeStatus($request->status, $id);

        // if ($paymentDetail->status == Status::APPROVED) {
        //     $notification = new PaymentApprovedNotification($paymentDetail->course->slug, $paymentDetail->course->title, $paymentDetail);
        //     $this->studentsNotificationService->notify($notification, $paymentDetail->user);
        // } else if ($paymentDetail->status == Status::REJECTED) {
        //     $notification = new PaymentRejectedNotification($paymentDetail->course->slug, $paymentDetail->course->title);
        //     $this->studentsNotificationService->notify($notification, $paymentDetail->user);
        // }


        Toastr::success(__('messages.payment_detail_changed'), __('status.success'));

        return redirect()->back();
    }
}
