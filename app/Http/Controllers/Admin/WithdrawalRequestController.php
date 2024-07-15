<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WithdrawalRequest;
use App\Services\WithdrawalRequestService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yoeunes\Toastr\Facades\Toastr;

class WithdrawalRequestController extends Controller
{
    protected $withdrawalRequestService;
    public function __construct(WithdrawalRequestService $withdrawalRequestService)
    {
        $this->withdrawalRequestService = $withdrawalRequestService;
    }
    public function index()
    {
        $withdrawalRequests = $this->withdrawalRequestService->getWithdrawalRequests();
        return view('admin.withdrawal.index', compact('withdrawalRequests'));
    }


    public function status(Request $request, WithdrawalRequest $withdrawalRequest)
    {
        DB::beginTransaction();
        try {
            $this->withdrawalRequestService->changeStatus($request->all(), $withdrawalRequest);
            DB::commit();
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            Toastr::error($e->getMessage());
            return redirect()->back();
        }
    }

    // show
    public function show(WithdrawalRequest $withdrawalRequest)
    {
        $buttons = $this->getActionButtons($withdrawalRequest->status);

        return view('admin.withdrawal.show', compact('withdrawalRequest', 'buttons'));
    }

    private function getActionButtons($status)
    {
        $buttons = [
            0 => [
                ['status' => 1, 'class' => 'btn-success', 'icon' => 'fas fa-check', 'title' => __('main.approve')],
                ['status' => 2, 'class' => 'btn-danger', 'icon' => 'fas fa-times', 'title' => __('main.reject')]
            ],
            1 => [
                ['status' => 2, 'class' => 'btn-danger', 'icon' => 'fas fa-times', 'title' => __('main.reject')]
            ],
            2 => [
                ['status' => 1, 'class' => 'btn-success', 'icon' => 'fas fa-check', 'title' => __('main.approve')]
            ]
        ];

        return $buttons[$status] ?? [];
    }
}
