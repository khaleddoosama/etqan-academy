<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\PaymentDataTable;
use App\Exports\FilteredPaymentsExport;
use App\Http\Controllers\Controller;
use App\Services\PaymentDetailService;
use App\Services\PaymentStatisticsService;
use App\Services\PaymentStrategy\PaymentContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Yoeunes\Toastr\Facades\Toastr;

class PaymentDetailController extends Controller
{


    public function __construct(
        protected PaymentDetailService $paymentDetailService,
        protected PaymentContext $paymentContext,
        protected PaymentStatisticsService $paymentStatisticsService,
    ) {
        $this->middleware('permission:payment_detail.list')->only('index');
        $this->middleware('permission:payment_detail.show')->only('show');
        $this->middleware('permission:payment_detail.status')->only('status');
        $this->middleware('permission:payment_detail.list')->only('download');
    }

    // index
    public function index(Request $request)
    {
        $statistics = $this->paymentStatisticsService->getInstapayStatistics();
        $users = $this->paymentStatisticsService->getUsersForFilter();
        $paymentStatuses = $this->paymentStatisticsService->getPaymentStatuses();
        $gateways = $this->paymentStatisticsService->getGateways();
        $coupons = $this->paymentStatisticsService->getCouponsForFilter();

        return view('admin.payment_detail.index', compact(
            'statistics',
            'users',
            'paymentStatuses',
            'gateways',
            'coupons'
        ));
    }

    public function data(PaymentDataTable $dataTable, Request $request)
    {
        if ($request->ajax()) {
            return $dataTable->dataTable($dataTable->query(new \App\Models\Payment))->make(true);
        }
        abort(403, 'Unauthorized access.');
    }

    public function show($id)
    {
        $payment = $this->paymentDetailService->getPayment($id, ['*'], [
            'user',
            'paymentItems.courseInstallment.course',
            'paymentItems.packagePlan',
            'paymentItems.course',
            'paymentItems.courseInstallment',
        ]);
        return view('admin.payment_detail.show', compact('payment'));
    }

    public function updateAmountConfirmed(Request $request, $id)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $this->paymentDetailService->updateAmountConfirmed($data['amount'], $id);

        Toastr::success(__('messages.amount_updated_successfully'), __('status.success'));

        return redirect()->back();
    }

    public function status(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $this->paymentDetailService->changeStatus($request->status, $id);

            DB::commit();

            Toastr::success(__('messages.payment_detail_changed'), __('status.success'));

            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            Toastr::error($e->getMessage());
            return redirect()->back();
        }
    }

    // export
    public function export()
    {
        // run artisan command report:weekly-payments
        Artisan::call('report:weekly-payments');

        return redirect()->back();
    }

    // download filtered payments
    public function download(Request $request)
    {
        try {
            $filters = $request->only([
                'search',
                'user_id',
                'gateway',
                'status',
                'coupon_id',
                'from_created_at',
                'to_created_at'
            ]);

            $filename = 'payments_' . now()->format('Y_m_d_H_i_s') . '.xlsx';

            return Excel::download(new FilteredPaymentsExport($filters), $filename);
        } catch (\Exception $e) {
            Log::error('Payment download error: ' . $e->getMessage());
            Toastr::error(__('messages.download_failed'));
            return redirect()->back();
        }
    }
}
