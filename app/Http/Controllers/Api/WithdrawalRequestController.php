<?php

namespace App\Http\Controllers\Api;

use App\Events\CreateWithdrawalRequestEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\WithdrawalRequestRequest;
use App\Notifications\WithdrawalRequestNotification;
use App\Services\AdminNotificationService;
use App\Services\WithdrawalRequestService;
use Illuminate\Support\Arr;

class WithdrawalRequestController extends Controller
{
    use ApiResponseTrait;
    protected WithdrawalRequestService $WithdwawalRequestService;
    private AdminNotificationService $adminNotificationService;

    public function __construct(WithdrawalRequestService $withdrawalRequestService, AdminNotificationService $adminNotificationService)
    {
        $this->WithdwawalRequestService = $withdrawalRequestService;
        $this->adminNotificationService = $adminNotificationService;
    }

    public function store(WithdrawalRequestRequest $request)
    {
        $data = $request->validated();
        $data = Arr::except($data, ['password']);

        $withdrawalRequest = $this->WithdwawalRequestService->store($data);

        // $notification = new WithdrawalRequestNotification($withdrawalRequest->user->name, $withdrawalRequest->id);
        // $this->adminNotificationService->notifyAdmins($notification,['withdrawal.list', 'withdrawal.show']);
        event (new CreateWithdrawalRequestEvent([], ['userName' => $withdrawalRequest->user->name, 'withdrawalRequestId' => $withdrawalRequest->id]));
        return $this->apiResponse($withdrawalRequest, __('messages.withdrawal_request_sent'), 201);
    }
}
