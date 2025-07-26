<?php

namespace App\Services\PaymentGateway;

use App\DTO\FawaterakPayloadDTO;
use App\DTO\PaymentRecordDTO;
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
        protected PaymentRepositoryInterface $paymentRepository,
        protected FawaterakApiClientService $apiClient,
        protected FawaterakPayloadBuilderService $payloadBuilder
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
        return $this->apiClient->getPaymentMethods();
    }

    public function executePayment(array $data)
    {
        $user = auth()->user();

        $carts = $this->cartService->getForUser(
            $user->id,
            ['price', 'quantity', 'course_id', 'course_installment_id', 'package_plan_id'],
            ['course', 'courseInstallment', 'packagePlan']
        );

        $totalPriceBeforeCoupon = $this->cartService->getTotalPriceForUser($user->id);
        $couponData = null;
        $coupon = null;
        $finalPriceAfterCoupon = $totalPriceBeforeCoupon;

        if (!empty($data['coupon_code'])) {
            $coupon = $this->couponService->findByCode($data['coupon_code']);
            $couponData = $this->couponService->checkCoupon($data['coupon_code']);
            $finalPriceAfterCoupon = $couponData['total'];
        }

        if ($data['amount'] != $finalPriceAfterCoupon) {
            throw new Exception('Invalid amount');
        }

        $dto = new FawaterakPayloadDTO($data, $user, $carts, $totalPriceBeforeCoupon, $couponData);
        $preparedPayload = $this->payloadBuilder->buildApiPayload($dto);

        $response = $this->apiClient->initInvoice($preparedPayload);

        if ($response['status'] != 'success') {
            throw new Exception(json_encode($response['message']));
        }
        $responseKeys = [
            'invoice_id' => $response['data']['invoice_id'],
            'invoice_key' => $response['data']['invoice_key'],
            'payment_data' => $response['data']['payment_data'],
        ];

        $paymentDto = new PaymentRecordDTO(
            $responseKeys,
            $user,
            $carts,
            $finalPriceAfterCoupon,
            $totalPriceBeforeCoupon,
            $coupon,
            $data['payment_method_id'],
            $data['payment_method']
        );

        $paymentData = $this->payloadBuilder->buildPaymentData($paymentDto);
        $this->paymentRepository->createWithItems(
            $paymentData,
            'paymentItems',
            $paymentData['paymentItems']->toArray()
        );

        return $response;
    }

    public function executeInstapayPayment(array $data): array
    {
        $user = auth()->user();

        $carts = $this->cartService->getForUser(
            $user->id,
            ['price', 'quantity', 'course_id', 'course_installment_id', 'package_plan_id'],
            ['course', 'courseInstallment', 'packagePlan']
        );

        $totalPriceBeforeCoupon = $this->cartService->getTotalPriceForUser($user->id);
        $couponData = null;
        $coupon = null;
        $finalPriceAfterCoupon = $totalPriceBeforeCoupon;

        if (!empty($data['coupon_code'])) {
            $coupon = $this->couponService->findByCode($data['coupon_code']);
            $couponData = $this->couponService->checkCoupon($data['coupon_code']);
            $finalPriceAfterCoupon = $couponData['total'];
        }

        // Create payment record for Instapay (pending admin review)
        $paymentData = [
            'user_id' => $user->id,
            'gateway' => 'instapay',
            'amount_before_coupon' => $totalPriceBeforeCoupon,
            'amount_after_coupon' => $finalPriceAfterCoupon,
            'amount_confirmed' => $finalPriceAfterCoupon,
            'coupon_id' => $coupon?->id,
            'discount' => $coupon?->discount,
            'type' => $coupon?->type,
            'payment_method' => 'instapay',
            'status' => 'pending'
        ];

        // Handle file upload
        if (isset($data['transfer_image']) && $data['transfer_image']) {
            $paymentData['transfer_image'] = $data['transfer_image'];
        }

        $paymentItemsData = $carts->map(fn($cart) => [
            'course_installment_id' => $cart->course_installment_id,
            'course_id' => $cart->course_id,
            'package_plan_id' => $cart->package_plan_id,
            'amount' => $cart->price * $cart->quantity,
            'payment_type' => $cart->course_installment_id ? PaymentType::INSTALLMENT : PaymentType::CASH,
        ]);

        $payment = $this->paymentRepository->createWithItems(
            $paymentData,
            'paymentItems',
            $paymentItemsData->toArray()
        );

        return [
            'status' => 'success',
            'message' => 'Payment submitted for review',
            'data' => [
                'payment_id' => $payment->id,
                'status' => 'pending'
            ]
        ];
    }
}
