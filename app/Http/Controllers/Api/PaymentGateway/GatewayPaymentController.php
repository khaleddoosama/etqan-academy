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
        return $methods;
    }

    public function pay(PaymentGatewatRequest $request)
    {
        $data =  $request->validated();

        $response = $this->paymentService->executePayment($data);

        return $response;
    }
}
