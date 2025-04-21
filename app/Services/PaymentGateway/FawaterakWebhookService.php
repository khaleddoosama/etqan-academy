<?php

namespace App\Services\PaymentGateway;

use Illuminate\Support\Facades\Log;

class FawaterakWebhookService
{


    protected string $secretKey;

    public function __construct()
    {
        $this->secretKey = config('fawaterak.api_key');
    }

    protected function isValidHash(array $data): bool
    {
        if (isset($data['invoice_id'])) {
            // Paid Webhook
            $queryParam = "InvoiceId={$data['invoice_id']}&InvoiceKey={$data['invoice_key']}&PaymentMethod={$data['payment_method']}";
        } elseif (isset($data['referenceId'])) {
            // Expired Webhook
            $queryParam = "referenceId={$data['referenceId']}&PaymentMethod={$data['paymentMethod']}";
        } else {
            return false;
        }

        $generatedHash = hash_hmac('sha256', $queryParam, $this->secretKey, false);

        return $generatedHash === ($data['hashKey'] ?? '');
    }

    public function processWebhookSuccess($data)
    {
        Log::info('Fawaterak Webhook Success Data:', $data);
        // Process the success data here
        if (!$this->isValidHash($data)) {
            Log::warning('Invalid Fawaterak Webhook Hash');
            return false;
        }


        
    }
}
