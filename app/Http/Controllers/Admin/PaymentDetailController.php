<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PaymentStatusEnum;
use App\Events\PaymentApprovedEvent;
use App\Events\PaymentRejectedEvent;
use App\Http\Controllers\Controller;
use App\Services\PaymentDetailService;
use App\Services\PaymentStrategy\PaymentContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yoeunes\Toastr\Facades\Toastr;
use DataTables;

class PaymentDetailController extends Controller
{


    public function __construct(
        protected PaymentDetailService $paymentDetailService,
        protected PaymentContext $paymentContext,

    ) {

        $this->middleware('permission:payment_detail.list')->only('index');
        $this->middleware('permission:payment_detail.show')->only('show');
        $this->middleware('permission:payment_detail.status')->only('reply');
    }

    // index
    public function index(Request $request)
    {
        return view('admin.payment_detail.index');
    }

    public function data(Request $request)
    {
        if ($request->ajax()) {

            $payments = $this->paymentDetailService->getPayments();

            return DataTables::of($payments)
                ->addIndexColumn()
                ->addColumn('user_name', fn($row) => optional($row->user)->name)
                ->addColumn('user_email', fn($row) => optional($row->user)->email)
                ->addColumn('user_phone', fn($row) => optional($row->user)->phone)
                ->addColumn('coupon_code', fn($row) => optional($row->coupon)->code)
                ->addColumn('gateway', fn($row) => $this->formatGateway($row))
                ->editColumn('status', fn($row) => $row->status->badge())
                ->editColumn('created_at', fn($row) => $row->created_at->format('Y-m-d H:i'))
                ->addColumn('action', fn($row) => $this->getActionButtons($row))
                ->rawColumns(['action', 'status', 'gateway'])
                ->make(true);
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
            'amount' => 'required|numeric',
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

    private function formatGateway($payment)
    {
        $gateway = ucfirst($payment->gateway ?? 'fawaterak');

        if ($payment->gateway === 'instapay') {
            $badge = '<span class="badge badge-warning">Instapay</span>';
            if ($payment->status->value === 'pending') {
                $badge .= ' <span class="badge badge-info badge-sm">Needs Review</span>';
            }
            return $badge;
        }

        return '<span class="badge badge-primary">' . $gateway . '</span>';
    }

    private function getActionButtons($payment)
    {
        $buttons = '<a href="' . route('admin.payment_details.show', $payment->id) . '" target="_blank" class="btn btn-sm btn-success ml-2">
            <i class="fas fa-eye"></i>
        </a>';

        // Add quick action buttons for pending Instapay payments
        if ($payment->gateway === 'instapay' && $payment->status->value === 'pending') {
            $buttons .= '
            <form action="' . route('admin.payment_details.status', $payment->id) . '" method="POST" class="d-inline ml-1 quick-approve-form" data-payment-id="' . $payment->id . '">
                ' . csrf_field() . '
                ' . method_field('PUT') . '
                <input type="hidden" name="status" value="paid">
                <button type="button" class="btn btn-sm btn-success quick-approve-btn" title="Quick Approve">
                    <i class="fas fa-check"></i>
                </button>
            </form>
            <form action="' . route('admin.payment_details.status', $payment->id) . '" method="POST" class="d-inline ml-1 quick-reject-form" data-payment-id="' . $payment->id . '">
                ' . csrf_field() . '
                ' . method_field('PUT') . '
                <input type="hidden" name="status" value="cancelled">
                <button type="button" class="btn btn-sm btn-danger quick-reject-btn" title="Quick Reject">
                    <i class="fas fa-times"></i>
                </button>
            </form>';
        }

        return $buttons;
    }
}
