<?php

namespace App\Services\PaymentGateway;

use App\Enums\PaymentType;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use App\Services\CartService;
use App\Services\CouponService;
use Exception;
use Illuminate\Support\Facades\Http;

class FawaterakPaymentGatewayService
{
    protected string $apiKey;
    protected string $baseUrl;

    public function __construct(
        protected CartService $cartService,
        protected CouponService $couponService,
        protected PaymentRepositoryInterface $paymentRepository
    ) {
        $this->apiKey = config('fawaterak.api_key');
        $this->baseUrl = config('fawaterak.api_url');
    }

    protected function fawaterakRequest()
    {
        return Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ]);
    }

    public function getPaymentMethods()
    {
        return $this->fawaterakRequest()
            ->get("{$this->baseUrl}/getPaymentmethods")
            ->json();
    }

    public function executePayment(array $data)
    {
        $prepared = $this->preparePayment($data);

        $this->paymentRepository->createWithItems(
            $prepared['paymentData'],
            $prepared['paymentData']['paymentItems']->toArray()
        );

        return $this->fawaterakRequest()
            ->post("{$this->baseUrl}/invoiceInitPay", $prepared['payload'])
            ->json();
    }

    protected function preparePayment(array $data): array
    {
        $user = auth()->user();

        $carts = $this->cartService->getForUser(
            $user->id,
            ['price', 'quantity', 'course_id', 'course_installment_id'],
            ['course', 'courseInstallment']
        );

        $totalPrice = $this->cartService->getTotalPriceForUser($user->id);
        $couponData = null;
        $coupon = null;
        $finalPrice = $totalPrice;

        if (!empty($data['coupon_code'])) {
            $coupon = $this->couponService->findByCode($data['coupon_code']);
            $couponData = $this->couponService->checkCoupon($data['coupon_code']);
            $finalPrice = $couponData['total'];
        }

        if ($data['amount'] != $finalPrice) {
            throw new Exception('Invalid amount');
        }

        return [
            'payload' => $this->buildPayload($data, $user, $carts, $finalPrice, $couponData),
            'paymentData' => $this->buildPaymentData($user, $carts, $finalPrice, $totalPrice, $coupon)
        ];
    }

    protected function buildPayload($data, $user, $carts, $finalPrice, $couponData)
    {
        $frontendUrl = rtrim(env('FRONTEND_URL'), '/') . '/fawaterak/payment/';

        $payload = [
            'payment_method_id' => $data['payment_method_id'],
            'cartTotal' => $finalPrice,
            'currency' => 'EGP',
            'customer' => [
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'phone' => $user->phone,
                'address' => $user->address,
            ],
            'cartItems' => $carts->map(fn($cart) => [
                'name' => $cart->course->title,
                'price' => $cart->price,
                'quantity' => $cart->quantity,
            ]),
            'redirectionUrls' => [
                'successUrl' => "{$frontendUrl}success",
                'failUrl' => "{$frontendUrl}fail",
                'pendingUrl' => "{$frontendUrl}pending",
            ],
            'payLoad' => [
                'user_id' => $user->id,
                'course_installment_id' => $carts->pluck('course_installment_id'),
                'course_id' => $carts->pluck('course_id'),
                'coupon_code' => $data['coupon_code'] ?? null,
            ],
        ];

        if ($couponData) {
            $payload['discountData'] = [
                'type' => $couponData['type'] === 'fixed' ? 'literal' : 'pcg',
                'value' => $couponData['discount'],
            ];
        }

        return $payload;
    }

    protected function buildPaymentData($user, $carts, $finalPrice, $totalPrice, $coupon)
    {
        return [
            'user_id' => $user->id,
            'coupon_id' => $coupon?->id,
            'amount_before_coupon' => $totalPrice,
            'amount_after_coupon' => $finalPrice,
            'discount' => $coupon?->discount,
            'type' => $coupon?->type,
            'paymentItems' => $carts->map(fn($cart) => [
                'course_installment_id' => $cart->course_installment_id,
                'course_id' => $cart->course_id,
                'amount' => $cart->price * $cart->quantity,
                'payment_type' => $cart->course_installment_id ? PaymentType::INSTALLMENT : PaymentType::CASH,
            ]),
        ];
    }
}
