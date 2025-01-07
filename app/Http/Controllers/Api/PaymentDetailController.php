<?php

namespace App\Http\Controllers\Api;

use App\Events\CreatePaymentDetailEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PaymentDetailRequest;
use App\Services\PaymentDetailService;
use Illuminate\Http\Request;

class PaymentDetailController extends Controller
{
    use ApiResponseTrait;
    private PaymentDetailService $paymentDetailService;

    public function __construct(PaymentDetailService $paymentDetailService)
    {
        $this->paymentDetailService = $paymentDetailService;
    }

    public function store(PaymentDetailRequest $request)
    {
        $paymentDetail = $this->paymentDetailService->store($request->validated());

        if ($paymentDetail) {
            event(new CreatePaymentDetailEvent([], ['paymentDetailId' => $paymentDetail->id]));
            return $this->apiResponse($paymentDetail, __('messages.payment_detail_created'), 201);
        } else {
            return $this->apiResponse(null, __('messages.payment_detail_fail'), 400);
        }
    }
}
