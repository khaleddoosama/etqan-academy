<?php

namespace App\Exports;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FilteredPaymentsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithEvents
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
            ->withPaymentItems()
            ->search($this->filters['search'] ?? null)
            ->filterByUser($this->filters['user_id'] ?? null)
            ->filterByGateway($this->filters['gateway'] ?? null)
            ->filterByStatus($this->filters['status'] ?? null)
            ->filterByDateRangePaidAt(
                $this->filters['from_paid_at'] ?? null,
                $this->filters['to_paid_at'] ?? null
            )
            ->filterByCoupon($this->filters['coupon_id'] ?? null)
            ->orderBy('created_at', 'desc');
    }

    public function headings(): array
    {
        return [
            'ID',
            'User Name',
            'Email',
            'Phone',
            'Course/Service',
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
            $this->formatCourseOrService($payment),
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

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Get the highest row number (after all data is written)
                $highestRow = $event->sheet->getHighestRow();
                $nextRow = $highestRow + 1;

                // Calculate totals from the query
                $payments = $this->query()->get();
                $totalOriginalAmount = $payments->sum('amount_before_coupon');
                $totalConfirmedAmount = $payments->sum('amount_confirmed');
                $totalCount = $payments->count();                // Add totals row
                $event->sheet->setCellValue('A' . $nextRow, 'TOTALS');
                $event->sheet->setCellValue('B' . $nextRow, "Total Records: {$totalCount}");
                $event->sheet->setCellValue('I' . $nextRow, $totalOriginalAmount);
                $event->sheet->setCellValue('J' . $nextRow, $totalConfirmedAmount);

                // Style the totals row
                $event->sheet->getStyle('A' . $nextRow . ':N' . $nextRow)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 12,
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'argb' => 'FFF0F0F0',
                        ],
                    ],
                    'borders' => [
                        'top' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                        ],
                    ],
                ]);
            },
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

    private function formatCourseOrService($payment): string
    {
        $serviceTitle = '';

        foreach ($payment->paymentItems as $paymentItem) {
            $serviceTitle .= $paymentItem->getServiceTitleAttribute() . ', ';
        }
        return rtrim($serviceTitle, ', ') ?: 'No Service';
    }
}
