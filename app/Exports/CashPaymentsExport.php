<?php

namespace App\Exports;

use App\Enums\PaymentType;
use App\Enums\Status;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CashPaymentsExport implements FromCollection, WithHeadings, WithStyles, WithColumnFormatting, WithMapping
{
    protected $startOfWeek;
    protected $endOfWeek;

    public function __construct()
    {
        $this->startOfWeek = now()->previous('friday')->startOfDay();
        $this->endOfWeek = now()->next('friday')->startOfDay();
    }

    public function collection()
    {
        Log::info('Cash Payments Exported ' . now()->toDateString());

        return Payment::with(['user', 'courseInstallment.course'])
            ->where('payment_type', PaymentType::CASH->value)
            ->whereBetween('approved_at', [$this->startOfWeek, $this->endOfWeek])
            ->where('status', Status::APPROVED->value)
            ->get();
    }

    public function map($payment): array
    {
        $title = "";
        foreach ($payment->paymentItems as $paymentItem) {
            $courseInstallment = $paymentItem->courseInstallment;
            $course = $courseInstallment->course;

            $title .= $course->title . ', ';
        };
        return [
            $payment->id,
            $payment->user->name ?? 'N/A',
            $payment->user->email ?? 'N/A',
            strval($payment->user->phone ?? 'N/A'),
            $title,
            strval($payment->whatsapp_number ?? 'N/A'),
            $payment->payment_type->value ?? 'N/A',
            $payment->payment_method->value ?? 'N/A',
            strval($payment->transfer_number ?? 'N/A'), // Explicitly cast as string
            Storage::url($payment->transfer_image) ?? 'N/A',
            $payment->amount ?? 0,
            $payment->created_at->toDateTimeString(),
            $payment->approved_at->toDateTimeString(),
        ];
    }


    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Email',
            'Phone',
            'Course',
            'WhatsApp Number',
            'Payment Type',
            'Payment Method',
            'Transfer Number',
            'Transfer Image',
            'Amount',
            'Created At',
            'Approved At',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style the header row
        $sheet->getStyle('A1:M1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => 'solid',
                'startColor' => ['rgb' => '4CAF50'], // Green background
            ],
        ]);

        // Auto size all columns
        foreach (range('A', 'M') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        return $sheet;
    }



    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_TEXT, // Column D is 'Phone'
            'F' => NumberFormat::FORMAT_TEXT, // Column F is 'WhatsApp Number'
            'J' => NumberFormat::FORMAT_TEXT, // Column J is 'Transfer Number'
            'L' => NumberFormat::FORMAT_DATE_DDMMYYYY, // Column L is 'Created At'
        ];
    }
}
