<?php

namespace App\Http\Controllers\Api\PaymentGateway;

use App\Http\Controllers\Api\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PaymentGatewatRequest;
use App\Services\PaymentGateway\FawaterakPaymentGatewayService;

class GatewayPaymentController extends Controller
{
    use ApiResponseTrait;

    protected $paymentService;

    public function __construct(FawaterakPaymentGatewayService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function paymentMethods()
    {
        $methods = $this->paymentService->getPaymentMethods();
        return $this->apiResponse($methods, __('messages.payment_methods_success'), 200);
    }

    public function pay(PaymentGatewatRequest $request)
    {
        $data =  $request->validated();

        $payment = $this->paymentService->executePayment($data);

        return $this->apiResponse($payment, __('messages.payment_success'), 200);
    }
}
