<?php
namespace App\Services\PaymentGateway;
use Illuminate\Support\Facades\Http;


class FawaterakApiClientService
{
    protected string $apiKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('fawaterak.api_key');
        $this->baseUrl = config('fawaterak.api_url');
    }

    protected function request()
    {
        return Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ]);
    }

    public function getPaymentMethods()
    {
        return $this->request()->get("{$this->baseUrl}/getPaymentmethods")->json();
    }

    public function initInvoice(array $payload)
    {
        return $this->request()->post("{$this->baseUrl}/invoiceInitPay", $payload)->json();
    }
}
