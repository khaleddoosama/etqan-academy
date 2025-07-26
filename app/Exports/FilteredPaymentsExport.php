<?php

namespace App\Exports;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FilteredPaymentsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        return Payment::query()
            ->withRelations()
            ->search($this->filters['search'] ?? null)
            ->filterByUser($this->filters['user_id'] ?? null)
            ->filterByGateway($this->filters['gateway'] ?? null)
            ->filterByStatus($this->filters['status'] ?? null)
            ->filterByDateRange(
                $this->filters['from_created_at'] ?? null,
                $this->filters['to_created_at'] ?? null
            )
            ->orderBy('created_at', 'desc');
    }

    public function headings(): array
    {
        return [
            'ID',
            'User Name',
            'Email',
            'Phone',
            'Gateway',
            'Coupon Code',
            'Payment Method',
            'Original Amount',
            'Amount Confirmed',
            'Status',
            'Created At',
            'Paid At',
            "Transfer Image URL",
        ];
    }

    public function map($payment): array
    {
        return [
            $payment->id,
            optional($payment->user)->name,
            optional($payment->user)->email,
            optional($payment->user)->phone,
            $this->formatGateway($payment),
            optional($payment->coupon)->code,
            $payment->payment_method,
            $payment->amount_before_coupon,
            $payment->amount_confirmed,
            $payment->status->value,
            $payment->created_at->format('Y-m-d H:i:s'),
            $payment->paid_at ? $payment->paid_at->format('Y-m-d H:i:s') : null,
            $payment->transfer_image_url,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }

    private function formatGateway($payment): string
    {
        if ($payment->gateway === 'instapay') {
            return 'Instapay';
        }

        if ($payment->gateway === 'fawaterak') {
            return 'Fawaterak';
        }

        if ($payment->gateway === 'paymob') {
            return 'Paymob';
        }

        return ucfirst($payment->gateway ?? 'Unknown');
    }
}
