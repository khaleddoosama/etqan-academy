<?php

namespace App\Http\Controllers\Api;

use App\Events\CreateWithdrawalRequestEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\WithdrawalRequestRequest;
use App\Services\WithdrawalRequestService;
use Illuminate\Support\Arr;

class WithdrawalRequestController extends Controller
{
    use ApiResponseTrait;
    protected WithdrawalRequestService $WithdwawalRequestService;

    public function __construct(WithdrawalRequestService $withdrawalRequestService)
    {
        $this->WithdwawalRequestService = $withdrawalRequestService;
    }

    public function store(WithdrawalRequestRequest $request)
    {
        $data = $request->validated();
        $data = Arr::except($data, ['password']);

        $withdrawalRequest = $this->WithdwawalRequestService->store($data);

        event (new CreateWithdrawalRequestEvent([], ['userName' => $withdrawalRequest->user->name, 'withdrawalRequestId' => $withdrawalRequest->id]));
        return $this->apiResponse($withdrawalRequest, __('messages.withdrawal_request_sent'), 201);
    }
}
