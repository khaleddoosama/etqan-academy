<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\WithdrawalRequestRequest;
use App\Services\WithdrawalRequestService;
use Illuminate\Http\Request;
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

        return $this->apiResponse($withdrawalRequest, 'Withdrawal Request sent successfully', 201);
    }
}
