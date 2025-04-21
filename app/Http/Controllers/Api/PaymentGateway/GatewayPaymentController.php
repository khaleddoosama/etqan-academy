<?php

namespace App\Http\Controllers\Api\PaymentGateway;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PaymentGatewatRequest;
use App\Services\PaymentGateway\FawaterakPaymentGatewayService;

class GatewayPaymentController extends Controller
{
    protected $paymentService;

    public function __construct(FawaterakPaymentGatewayService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function paymentMethods()
    {
        $methods = $this->paymentService->getPaymentMethods();
        return response()->json($methods);
    }

    public function pay(PaymentGatewatRequest $request)
    {
        $data =  $request->validated();

        $payload = $this->paymentService->preparePayment($data);

        $payment = $this->paymentService->executePayment($payload);
        return response()->json($payment);
    }
}
