<?php

namespace App\Services\PaymentGateway;

use App\Services\CartService;
use Illuminate\Support\Facades\Http;

class FawaterakPaymentGatewayService
{
    protected $apiKey;
    protected $baseUrl;

    protected $cartService;
    public function __construct(CartService $cartService)
    {
        $this->apiKey = config('fawaterak.api_key');
        $this->baseUrl = config('fawaterak.api_url');

        $this->cartService = $cartService;
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

        $cart = $this->cartService->getForUser($user->id, ['']);

        $payload = [
            "payment_method_id" => $data['payment_method_id'],
            // "cartTotal" => $data['cartTotal'],
            "currency" => "EGP",
            "customer" => [
                "first_name" => $user->first_name,
                "last_name" => $user->last_name,
                "email" => $user->email,
                "phone" => $user->phone,
                "address" => $user->address
            ],
            "cartItems" => ,
            "redirectionUrls" => [
                "successUrl" => url('/api/payment/fawaterak/webhook/success'),
                "failUrl" => url('/api/payment/fawaterak/webhook/fail'),
                "pendingUrl" => url('/api/payment/fawaterak/webhook/pending'),
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
