<?php

namespace App\DataTables;

use App\Models\Payment;
use App\Traits\PaymentActionButtons;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Database\Eloquent\Builder;

class PaymentDataTable extends DataTable
{
    use PaymentActionButtons;

    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->addColumn('image', function ($row) {
                if ($row->transfer_image_url) {
                    return $row->transfer_image_url;
                }
                return '';
            })
            ->addColumn('user_name', fn($row) => optional($row->user)->name)
            ->addColumn('user_email', fn($row) => optional($row->user)->email)
            ->addColumn('user_phone', fn($row) => optional($row->user)->phone)
            ->addColumn('coupon_info', function ($row) {
                if ($row->coupon) {
                    $code = $row->coupon->code;
                    $discount = $row->discount;
                    $type = $row->type;

                    return [
                        'formatted' => "{$code} - {$discount}" . ($type === 'percentage' ? '%' : ' EGP')
                    ];
                }
                return null;
            })
            ->addColumn('gateway', fn($row) => $this->formatGateway($row))
            ->addColumn('service_titles', function ($row) {
                return $row->paymentItems->map(function ($item) {
                    return $item->serviceTitle;
                })->filter()->values()->toArray();
            })
            ->addColumn('amount_before_coupon', fn($row) => $row->amount_before_coupon)
            ->addColumn('amount_after_coupon', fn($row) => $row->amount_after_coupon)
            ->addColumn('amount_confirmed', fn($row) => $row->amount_confirmed)
            ->editColumn('status', fn($row) => $row->status->badge())
            ->editColumn('paid_at', fn($row) => optional($row->paid_at)?->format('Y-m-d H:i'))
            ->addColumn('action', fn($row) => $this->getActionButtons($row))
            ->rawColumns(['action', 'status', 'gateway', 'image']);
    }



    public function query(Payment $model): Builder
    {
        return $model->newQuery()
            ->withRelations()
            ->withPaymentItems()
            ->search(request('search'))
            ->filterByUser(request('user_id'))
            ->filterByGateway(request('gateway'))
            ->filterByStatus(request('status'))
            ->filterByCoupon(request('coupon_id'))
            ->filterByDateRange(request('from_paid_at'), request('to_paid_at'));
    }
}
