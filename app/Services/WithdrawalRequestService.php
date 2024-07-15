<?php
// app/Services/WithdrawalRequestService.php

namespace App\Services;


use App\Models\WithdrawalRequest;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Yoeunes\Toastr\Facades\Toastr;

class WithdrawalRequestService
{

    public function getWithdrawalRequests(): Collection
    {
        return WithdrawalRequest::with('user')->get();
    }

    // store
    public function store(array $data): WithdrawalRequest
    {
        $data['user_id'] = auth()->id();
        return WithdrawalRequest::create($data);
    }

    public function changeStatus(array $data, WithdrawalRequest $withdrawalRequest)
    {
        // check if data[status] is 1 then approve else reject
        if ($data['status'] == 1) {
            return $this->approve($withdrawalRequest);
        } elseif ($data['status'] == 2) {
            Toastr::success(__('messages.withdrawal_request_rejected'), __('status.success'));
            return $this->reject($withdrawalRequest, $data['rejected_reason']);
        }
    }

    // approve
    public function approve(WithdrawalRequest $withdrawalRequest)
    {
        // Get user points
        $user = $withdrawalRequest->user;
        $points = $user->points;
        // check if points is greater than or equal to withdrawal points
        if ($points >= $withdrawalRequest->points) {
            $user->points -= $withdrawalRequest->points;
            $user->save();
            $withdrawalRequest->status = 1;
            $withdrawalRequest->approved_by = auth()->id();
            $withdrawalRequest->approved_at = now();
            $withdrawalRequest->save();
            Toastr::success(__('messages.withdrawal_request_approved'), __('status.success'));
        } else {
            $this->reject($withdrawalRequest, 'Insufficient points');
            Toastr::error(__('messages.withdrawal_request_insufficient_points'), __('status.error'));
        }
        return $withdrawalRequest;
    }
    // reject
    public function reject(WithdrawalRequest $withdrawalRequest, string $rejected_reason)
    {
        $withdrawalRequest->status = 2;
        $withdrawalRequest->rejected_by = auth()->id();
        $withdrawalRequest->rejected_at = now();
        $withdrawalRequest->rejected_reason = $rejected_reason;
        $withdrawalRequest->save();
        return $withdrawalRequest;
    }
}
