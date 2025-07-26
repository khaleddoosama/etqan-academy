<?php

namespace App\Http\Controllers\Api\PaymentGateway;

use App\Http\Controllers\Api\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PaymentGatewatRequest;
use App\Http\Requests\Api\InstapayPaymentRequest;
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

    public function payInstapay(InstapayPaymentRequest $request)
    {
        $data = $request->validated();
        $canPay = $this->paymentService->canUserPay(auth()->id());
        if (!$canPay) {
            return $this->apiResponse([], 'You have already paid', 403);
        }
        $response = $this->paymentService->executeInstapayPayment($data);

        return $this->apiResponse($response['data'], $response['message'], 201);
    }
}
