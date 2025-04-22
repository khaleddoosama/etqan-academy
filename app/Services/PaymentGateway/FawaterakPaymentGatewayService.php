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

        $preparedPayload = $this->buildPayload($data, $user, $carts, $totalPrice, $couponData);

        $response = $this->fawaterakRequest()
            ->post("{$this->baseUrl}/invoiceInitPay", $preparedPayload)
            ->json();
        if ($response['status'] != 'success') {
            throw new Exception(json_encode($response['message']));
        }
        $uniqueKeys['invoice_id'] = $response['data']['invoice_id'];
        $uniqueKeys['invoice_key'] = $response['data']['invoice_key'];

        $paymentData = $this->buildPaymentData($uniqueKeys, $user, $carts, $finalPrice, $totalPrice, $coupon, $data['payment_method_id']);

        $this->paymentRepository->createWithItems(
            $paymentData,
            $paymentData['paymentItems']->toArray()
        );

        return $response;
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

    protected function buildPaymentData($uniqueKeys, $user, $carts, $finalPrice, $totalPrice, $coupon, $payment_method_id)
    {
        return [
            'user_id' => $user->id,
            'invoice_id' => $uniqueKeys['invoice_id'],
            'invoice_key' => $uniqueKeys['invoice_key'],
            'coupon_id' => $coupon?->id,
            'discount' => $coupon?->discount,
            'type' => $coupon?->type,
            'amount_before_coupon' => $totalPrice,
            'amount_after_coupon' => $finalPrice,
            'payment_method' => $payment_method_id,
            'paymentItems' => $carts->map(fn($cart) => [
                'course_installment_id' => $cart->course_installment_id,
                'course_id' => $cart->course_id,
                'amount' => $cart->price * $cart->quantity,
                'payment_type' => $cart->course_installment_id ? PaymentType::INSTALLMENT : PaymentType::CASH,
            ]),
        ];
    }
}
