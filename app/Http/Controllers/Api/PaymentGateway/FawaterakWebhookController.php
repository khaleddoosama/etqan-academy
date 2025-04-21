<?php

namespace App\Http\Controllers\PaymentGateway\ِApi;

use App\Http\Controllers\Controller;
use App\Services\PaymentGateway\FawaterakWebhookService;
use Illuminate\Http\Request;

class FawaterakWebhookController extends Controller
{
    protected $webhookService;

    public function __construct(FawaterakWebhookService $webhookService)
    {
        $this->webhookService = $webhookService;
    }

    public function handleSuccess(Request $request)
    {
        $data = $request->all();

        $this->webhookService->processWebhookSuccess($request->all());

        return response()->json(['message' => 'Webhook processed']);
    }
}
