<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\WithdrawalRequestRequest;
use App\Models\User;
use App\Notifications\WithdrawalRequestNotification;
use App\Services\AdminNotificationService;
use App\Services\WithdrawalRequestService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Notification;

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

        $notification = new WithdrawalRequestNotification($withdrawalRequest->user->name, $withdrawalRequest->id);
        $this->adminNotificationService->notifyAdmins($notification);

        return $this->apiResponse($withdrawalRequest, 'Withdrawal Request sent successfully', 201);
    }
}
