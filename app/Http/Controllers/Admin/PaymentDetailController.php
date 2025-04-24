<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Status;
use App\Events\PaymentApprovedEvent;
use App\Events\PaymentRejectedEvent;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\PaymentDetailService;
use App\Services\UserService;
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
        protected UserService $userService
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

        $payments = $this->paymentDetailService->getPayments();
        
        return DataTables::of($payments)
            ->addIndexColumn()
            ->addColumn('user_name', fn($row) => optional($row->user)->name)
            ->addColumn('user_email', fn($row) => optional($row->user)->email)
            ->addColumn('user_phone', fn($row) => optional($row->user)->phone)
            ->addColumn('coupon_code', fn($row) => optional($row->coupon)->code)
            ->editColumn('status', fn($row) => $row->status->badge())
            ->editColumn('created_at', fn($row) => $row->created_at->format('Y-m-d H:i'))
            ->addColumn('action', fn($row) => '
            <a href="' . route('admin.payment_details.show', $row->id) . '" target="_blank" class="btn btn-sm btn-success ml-2">
                <i class="fas fa-eye"></i>
            </a>
        ')
            ->rawColumns(['action', 'status'])
            ->make(true);
    }

    public function show($id)
    {
        $paymentDetail = $this->paymentDetailService->getPayment($id);
        return view('admin.payment_detail.show', compact('paymentDetail'));
    }

    public function updateAmount(Request $request, $id)
    {
        $data = $request->validate([
            'amount' => 'required|numeric',
        ]);

        $this->paymentDetailService->updateAmount($data['amount'], $id);

        Toastr::success(__('messages.amount_updated_successfully'), __('status.success'));

        return redirect()->back();
    }

    public function status(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $paymentDetail = $this->paymentDetailService->changeStatus($request->status, $id);

            if ($paymentDetail->status == Status::APPROVED) {
                event(new PaymentApprovedEvent([$paymentDetail->user_id], [
                    'courseSlug' => $paymentDetail->courseInstallment->course->slug,
                    'courseTitle' => $paymentDetail->courseInstallment->course->title,
                    'payment' => $paymentDetail
                ]));
            } else if ($paymentDetail->status == Status::REJECTED) {
                event(new PaymentRejectedEvent([$paymentDetail->user_id], [
                    'courseSlug' => $paymentDetail->courseInstallment->course->slug,
                    'courseTitle' => $paymentDetail->courseInstallment->course->title,
                ]));
            }

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
}
