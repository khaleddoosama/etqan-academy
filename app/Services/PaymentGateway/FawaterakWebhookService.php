<?php

namespace App\Services\PaymentGateway;

use App\Enums\PaymentStatusEnum;
use App\Enums\PaymentType;
use App\Events\PaymentApprovedEvent;
use App\Models\Cart;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use App\Services\PaymentStrategy\CashPayment;
use App\Services\PaymentStrategy\InstallmentPayment;
use App\Services\PaymentStrategy\PaymentContext;
use App\Services\StudentInstallmentService;
use App\Services\UserCoursesService;
use App\Services\UserService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class FawaterakWebhookService
{
    protected string $secretKey;

    public function __construct(
        protected PaymentRepositoryInterface $paymentRepository,
        protected PaymentContext $paymentContext,
        protected StudentInstallmentService $studentInstallmentService
    ) {
        $this->secretKey = config('fawaterak.api_key');
    }

    protected function isValidHash(array $data): bool
    {
        $queryParam = match (true) {
            isset($data['invoice_id']) => "InvoiceId={$data['invoice_id']}&InvoiceKey={$data['invoice_key']}&PaymentMethod={$data['payment_method']}", // Paid WebHook
            isset($data['referenceId']) => "referenceId={$data['referenceId']}&PaymentMethod={$data['paymentMethod']}", // Expired WebHook
            default => null,
        };

        if (!$queryParam) {
            return false;
        }

        $generatedHash = hash_hmac('sha256', $queryParam, $this->secretKey, false);
        return $generatedHash === ($data['hashKey'] ?? '');
    }

    protected function handleWebhook(array $data, PaymentStatusEnum $status, bool $requireValidHash = true): ?Model
    {
        $this->logWebhookData($status->value, $data);

        if ($requireValidHash && !$this->isValidHash($data)) {
            Log::warning("Invalid Fawaterak Webhook Hash for status: {$status->value}");
            return null;
        }

        $payment = $this->paymentRepository->where([
            'invoice_id' => $data['invoice_id'],
            'invoice_key' => $data['invoice_key']
        ])->first();

        if (!$payment) {
            Log::error("Payment not found for invoice: {$data['invoice_id']}");
            return null;
        }

        $updatePayload = [
            'status' => $status,
            'response_payload' => json_encode([...$data, 'created_at' => now()])
        ];

        if ($status === PaymentStatusEnum::Paid) {
            $updatePayload['paid_at'] = now();
        }

        $payment->update($updatePayload);

        return $payment;
    }

    protected function logWebhookData(string $status, array $data): void
    {
        $status = ucfirst(strtolower($status));
        Log::info("Fawaterak Webhook {$status} Data:", $data);
    }

    public function processWebhookPaid(array $data): void
    {
        $payment = $this->handleWebhook($data, PaymentStatusEnum::Paid);

        if (!$payment) {
            return;
        }

        $payment->load('paymentItems', 'coupon');

        foreach ($payment->paymentItems as $item) {
            if ($item->course_id) {
                $item->load(['course', 'courseInstallment', 'packagePlan']);
                $this->setPaymentStrategy($item->payment_type);

                $this->paymentContext->handlePayment($item, $payment->user_id);
            }
        }
        try {
            event(new PaymentApprovedEvent([$payment->user_id], [
                'payment' => $payment
            ]));
        } catch (\Exception $e) {
            Log::error("Error in PaymentApprovedEvent");
            Log::error($e->getMessage());
        }

        $coupon = $payment->coupon;
        if ($coupon) {
            $coupon->update([
                'usage_count' => $coupon->usage_count + 1
            ]);
        }

        // empty cart for user
        Cart::forUser($payment->user_id)->delete();
    }
    private function setPaymentStrategy(PaymentType $paymentType): void
    {
        $strategy = match ($paymentType) {
            PaymentType::CASH => new CashPayment(new UserService(), new UserCoursesService()),
            PaymentType::INSTALLMENT => new InstallmentPayment($this->studentInstallmentService, new UserService(), new UserCoursesService()),
            default => throw new \InvalidArgumentException('Invalid payment type.'),
        };

        $this->paymentContext->setPaymentStrategy($strategy);
    }
    public function processWebhookFailed(array $data): void
    {
        $this->handleWebhook($data, PaymentStatusEnum::Failed, false);
    }

    public function processWebhookCancelled(array $data): void
    {
        $this->handleWebhook($data, PaymentStatusEnum::Cancelled);
    }

    public function processWebhookRefund(array $data): void
    {
        $this->handleWebhook($data, PaymentStatusEnum::Refunded);
    }
}
