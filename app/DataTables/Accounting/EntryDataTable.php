<?php

namespace App\DataTables\Accounting;

use App\Models\Accounting\Entry;

use Yajra\DataTables\Services\DataTable;
use Illuminate\Database\Eloquent\Builder;

class EntryDataTable extends DataTable
{

    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->addColumn('category_name', fn($row) => optional($row->category)->name)
            ->addColumn('category_type', fn($row) => optional($row->category)->type?->value)
            ->addColumn('signed_amount', fn($row) => $row->signed_amount)
            ->editColumn('amount', fn($row) => number_format($row->amount, 2) . ' EGP')
            ->editColumn('transaction_date', fn($row) => $row->transaction_date?->format('Y-m-d'))
            ->addColumn('action', function ($row) {
                return view('admin.accounting.entry.actions', compact('row'))->render();
            })
            ->rawColumns(['action']);
    }



    public function query(Entry $model): Builder
    {
        return $model->newQuery()
            ->with(['category'])
            ->search(request('search'))
            ->category(request('category_id'))
            ->type(request('type'))
            ->dateRange(request('from_date'), request('to_date'));
    }
}
