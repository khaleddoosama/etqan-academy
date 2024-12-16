<?php

namespace App\Exports;

use App\Enums\PaymentType;
use App\Enums\Status;
use App\Models\PaymentDetails;
use App\Models\StudentInstallment;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InstallmentPaymentsExport implements FromCollection, WithHeadings, WithStyles, WithColumnFormatting, WithMapping
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
        Log::info('Installment Payments Exported: ' . now()->toDateString());

        return PaymentDetails::with(['user', 'courseInstallment.course'])
            ->where('payment_type', PaymentType::INSTALLMENT->value)
            ->whereBetween('approved_at', [$this->startOfWeek, $this->endOfWeek])
            ->where('status', Status::APPROVED->value)
            ->get();
    }

    public function map($payment): array
    {
        // Retrieve the installments for this payment
        $studentsInstallments = StudentInstallment::with(['student', 'courseInstallment.course'])
            ->where('student_id', $payment->user_id)
            ->where('course_installment_id', $payment->course_installment_id)
            ->orderBy('id', 'desc')
            ->get(['id', 'due_date']);

        // Calculate remaining installments
        $number_of_installments = $payment->courseInstallment->number_of_installments ?? 0;
        $number_of_installments_paid = $studentsInstallments->count();
        $remaining_installments = $number_of_installments - $number_of_installments_paid;

        // Get the last due date
        $lastInstallment = $studentsInstallments->first();
        $due_date = $lastInstallment && $lastInstallment->due_date ? $lastInstallment->due_date->toDateString() : 'N/A';

        return [
            $payment->id,
            $payment->user->name ?? 'N/A',
            $payment->user->email ?? 'N/A',
            strval($payment->user->phone ?? 'N/A'),
            $payment->courseInstallment->course->title ?? 'N/A',
            strval($payment->whatsapp_number ?? 'N/A'),
            $payment->payment_type->value ?? 'N/A',
            $payment->payment_method->value ?? 'N/A',
            strval($payment->transfer_number ?? 'N/A'),
            asset($payment->transfer_image) ?? 'N/A',
            $payment->amount ?? 0,
            $payment->created_at->toDateTimeString(),
            $payment->approved_at->toDateTimeString(),
            $due_date, // Add due_date to the mapped data
            (string) $remaining_installments, // Explicitly cast to string to ensure inclusion
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
            'Next Installment Date',
            'Remaining Installments',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:O1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => 'solid',
                'startColor' => ['rgb' => 'FF9800'], // Orange background
            ],
        ]);

        foreach (range('A', 'O') as $column) {
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
        ];
    }
}
