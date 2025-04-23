<?php

namespace App\Http\Controllers\Api\PaymentGateway;

use App\Http\Controllers\Api\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Services\PaymentGateway\FawaterakWebhookService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GatewayWebhookController extends Controller
{

    use ApiResponseTrait;
    protected $webhookService;

    public function __construct(FawaterakWebhookService $webhookService)
    {
        $this->webhookService = $webhookService;
    }

    public function handlePaid(Request $request)
    {
        $data = $request->all();

        $this->webhookService->processWebhookPaid($request->all());

        return $this->apiResponse(null, 'Webhook processed', 200);
    }

    public function handleFailed(Request $request)
    {
        $data = $request->all();

        $this->webhookService->processWebhookFailed($request->all());

        return $this->apiResponse(null, 'Webhook processed', 200);
    }

    public function handleCancelled(Request $request)
    {
        $data = $request->all();

        $this->webhookService->processWebhookCancelled($request->all());

        return $this->apiResponse(null, 'Webhook processed', 200);
    }

    public function handleRefund(Request $request)
    {
        $data = $request->all();

        $this->webhookService->processWebhookRefund($request->all());

        return $this->apiResponse(null, 'Webhook processed', 200);
    }
}
