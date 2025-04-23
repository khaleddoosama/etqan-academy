<?php

namespace App\Services\PaymentGateway;

use App\Enums\PaymentStatusEnum;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use Illuminate\Support\Facades\Log;

class FawaterakWebhookService
{
    protected string $secretKey;

    public function __construct(
        protected PaymentRepositoryInterface $paymentRepository
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

    protected function handleWebhook(array $data, PaymentStatusEnum $status, bool $requireValidHash = true): void
    {
        $this->logWebhookData($status->value, $data);

        if ($requireValidHash && !$this->isValidHash($data)) {
            Log::warning("Invalid Fawaterak Webhook Hash for status: {$status->value}");
            return;
        }

        $payment = $this->paymentRepository->where([
            'invoice_id' => $data['invoice_id'],
            'invoice_key' => $data['invoice_key']
        ])->first();

        if (!$payment) {
            Log::error("Payment not found for invoice: {$data['invoice_id']}");
            return;
        }

        $updatePayload = [
            'status' => $status,
            'response_payload' => json_encode([...$data, 'created_at' => now()])
        ];

        if ($status === PaymentStatusEnum::Paid) {
            $updatePayload['paid_at'] = now();
        }

        $payment->update($updatePayload);
    }

    protected function logWebhookData(string $status, array $data): void
    {
        $status = ucfirst(strtolower($status));
        Log::info("Fawaterak Webhook {$status} Data:", $data);
    }

    public function processWebhookPaid(array $data): void
    {
        $this->handleWebhook($data, PaymentStatusEnum::Paid);
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
