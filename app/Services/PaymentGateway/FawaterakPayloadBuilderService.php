<?php

namespace App\Services\PaymentGateway;

use App\DTO\FawaterakPayloadDTO;
use App\DTO\PaymentRecordDTO;
use App\Enums\PaymentType;

class FawaterakPayloadBuilderService
{
    public function buildApiPayload(FawaterakPayloadDTO $dto): array
    {
        $frontendUrl = rtrim(env('FRONTEND_URL'), '/') . '/fawaterak/payment/';

        $payload = [
            'payment_method_id' => $dto->inputData['payment_method_id'],
            'cartTotal' => $dto->totalPriceBeforeCoupon,
            'currency' => 'EGP',
            'customer' => [
                'first_name' => $dto->user->first_name,
                'last_name' => $dto->user->last_name,
                'email' => $dto->user->email,
                'phone' => $dto->user->phone,
                'address' => $dto->user->address,
            ],
            'cartItems' => $dto->carts->map(fn($cart) => [
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
                'user_id' => $dto->user->id,
                'course_installment_id' => $dto->carts->pluck('course_installment_id'),
                'course_id' => $dto->carts->pluck('course_id'),
                'coupon_code' => $dto->inputData['coupon_code'] ?? null,
            ],
        ];

        if ($dto->couponData) {
            $payload['discountData'] = [
                'type' => $dto->couponData['type'] === 'fixed' ? 'literal' : 'pcg',
                'value' => $dto->couponData['discount'],
            ];
        }

        return $payload;
    }


    public function buildPaymentData(PaymentRecordDTO $dto): array
    {
        return [
            'user_id' => $dto->user->id,
            'invoice_id' => $dto->uniqueKeys['invoice_id'],
            'invoice_key' => $dto->uniqueKeys['invoice_key'],
            'coupon_id' => $dto->coupon?->id,
            'discount' => $dto->coupon?->discount,
            'type' => $dto->coupon?->type,
            'amount_before_coupon' => $dto->totalPriceBeforeCoupon,
            'amount_after_coupon' => $dto->finalPriceAfterCoupon,
            'payment_method_id' => $dto->paymentMethodId,
            'payment_method' => $dto->paymentMethod,
            'paymentItems' => $dto->carts->map(fn($cart) => [
                'course_installment_id' => $cart->course_installment_id,
                'course_id' => $cart->course_id,
                'amount' => $cart->price * $cart->quantity,
                'payment_type' => $cart->course_installment_id ? PaymentType::INSTALLMENT : PaymentType::CASH,
            ]),
        ];
    }
}
