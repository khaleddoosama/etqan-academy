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
            ->addColumn('user_name', fn($row) => optional($row->user)->name)
            ->addColumn('user_email', fn($row) => optional($row->user)->email)
            ->addColumn('user_phone', fn($row) => optional($row->user)->phone)
            ->addColumn('coupon_code', fn($row) => optional($row->coupon)->code)
            ->addColumn('gateway', fn($row) => $this->formatGateway($row))
            ->editColumn('status', fn($row) => $row->status->badge())
            ->editColumn('created_at', fn($row) => $row->created_at->format('Y-m-d H:i'))
            ->addColumn('action', fn($row) => $this->getActionButtons($row))
            ->rawColumns(['action', 'status', 'gateway']);
    }



    public function query(Payment $model): Builder
    {
        return $model->newQuery()
            ->withRelations()
            ->search(request('search'))
            ->filterByUser(request('user_id'))
            ->filterByGateway(request('gateway'))
            ->filterByStatus(request('status'))
            ->filterByCoupon(request('coupon_id'))
            ->filterByDateRange(request('from_created_at'), request('to_created_at'));
    }

}
