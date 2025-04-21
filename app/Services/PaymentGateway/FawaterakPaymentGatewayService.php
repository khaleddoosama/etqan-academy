<?php

namespace App\Services\PaymentGateway;

use App\Services\CartService;
use App\Services\CouponService;
use Illuminate\Support\Facades\Http;

class FawaterakPaymentGatewayService
{
    protected $apiKey;
    protected $baseUrl;

    protected $cartService;
    protected $couponService;
    public function __construct(CartService $cartService, CouponService $couponService)
    {
        $this->apiKey = config('fawaterak.api_key');
        $this->baseUrl = config('fawaterak.api_url');

        $this->cartService = $cartService;
        $this->couponService = $couponService;
    }

    public function getPaymentMethods()
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->get($this->baseUrl . '/getPaymentmethods');

        return $response->json();
    }

    public function preparePayment($data)
    {
        $user = auth()->user();

        // $carts = $this->cartService->getForUser($user->id, ['']);

        $coupon = $this->couponService->findByCode($data['coupon_code']);
        // $couponData = $this->couponService->checkCoupon($data['coupon_code']);

        $payload = [
            "payment_method_id" => $data['payment_method_id'],
            // "cartTotal" => $data['cartTotal'],
            "cartTotal" => 100,
            'discountData' => [
                'type' => $coupon->type == 'fixed' ? "literal" : "pcg",
                'value' => 10 //
            ],
            "currency" => "EGP",
            "customer" => [
                "first_name" => $user->first_name,
                "last_name" => $user->last_name,
                "email" => $user->email,
                "phone" => $user->phone,
                "address" => $user->address
            ],
            // "cartItems" => $carts->map(function ($cart) {
            //     return [
            //         "name" => $cart->course->title,
            //         "price" => $cart->course->price,
            //         "quantity" => $cart->quantity,
            //     ];
            // }),
            "cartItems" => [
                [
                    "name" => "test",
                    "price" => "100",
                    "quantity" => 1
                ]
            ],
            "redirectionUrls" => [
                "successUrl" => env('FRONTEND_URL') . '/payment/success',
                "failUrl" =>    env('FRONTEND_URL') . '/payment/fail',
                "pendingUrl" => env('FRONTEND_URL') . '/payment/pending',
            ],
        ];

        return $payload;
    }

    public function executePayment($data)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post(
            $this->baseUrl . '/invoiceInitPay',
            $data
        );

        return $response->json();
    }
}
