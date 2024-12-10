<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PaymentDetailRequest;
use App\Notifications\PaymentDetailCreatedNotification;
use App\Services\AdminNotificationService;
use App\Services\PaymentDetailService;
use Illuminate\Http\Request;

class PaymentDetailController extends Controller
{
    use ApiResponseTrait;
    private PaymentDetailService $paymentDetailService;
    private AdminNotificationService $adminNotificationService;

    public function __construct(PaymentDetailService $paymentDetailService, AdminNotificationService $adminNotificationService)
    {
        $this->paymentDetailService = $paymentDetailService;
        $this->adminNotificationService = $adminNotificationService;
    }

    public function store(PaymentDetailRequest $request)
    {
        $paymentDetail = $this->paymentDetailService->store($request->validated());

        if ($paymentDetail) {
            $notification = new PaymentDetailCreatedNotification($paymentDetail->id);
            $this->adminNotificationService->notifyAdmins($notification, ['payment_detail.list', 'payment_detail.show']);

            return $this->apiResponse($paymentDetail, __('messages.payment_detail_created'), 201);
        } else {
            return $this->apiResponse(null, __('messages.payment_detail_fail'), 400);
        }
    }
}
