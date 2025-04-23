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
            ['price', 'quantity', 'course_id', 'course_installment_id'],
            ['course', 'courseInstallment']
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
            $paymentData['paymentItems']->toArray()
        );

        return $response;
    }
}
