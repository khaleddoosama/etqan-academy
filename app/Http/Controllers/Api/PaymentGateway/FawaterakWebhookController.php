<?php

namespace App\Http\Controllers\PaymentGateway\ِApi;

use App\Http\Controllers\Controller;
use App\Services\PaymentGateway\WebhookService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FawaterakWebhookController extends Controller
{
    protected $webhookService;

    public function __construct(WebhookService $webhookService)
    {
        $this->webhookService = $webhookService;
    }

    public function handle(Request $request, $status)
    {
        $data = $request->all();
        $data['status'] = $status;
        $this->webhookService->processFawaterakWebhook($request->all());

        return response()->json(['message' => 'Webhook processed']);
    }
}
