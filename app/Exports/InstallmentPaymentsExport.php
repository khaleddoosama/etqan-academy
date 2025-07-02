<?php

namespace App\Exports;

use App\Enums\PaymentType;
use App\Enums\Status;
use App\Models\Payment;
use App\Models\StudentInstallment;
use App\Services\PaymentDetailService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
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

    public function __construct(protected PaymentDetailService $paymentDetailService)
    {
        $this->startOfWeek = now()->previous('friday')->startOfDay();
        $this->endOfWeek = now()->next('friday')->startOfDay();
    }

    public function collection()
    {
        Log::info('Installment Payments Exported: ' . now()->toDateString());

        return $this->paymentDetailService->getWeeklyPaidInstallmetPayments($this->startOfWeek, $this->endOfWeek);
        // return Payment::with(['user', 'paymentItems.courseInstallment.course'])
        //     ->where('payment_type', PaymentType::INSTALLMENT->value)
        //     ->whereBetween('approved_at', [$this->startOfWeek, $this->endOfWeek])
        //     ->where('status', Status::APPROVED->value)
        //     ->get();
    }

    public function map($payment): array
    {
        // Initialize an array to store the data for the export
        $data = [];

        // Loop through each payment item associated with the payment
        foreach ($payment->paymentItems as $paymentItem) {
            $courseInstallment = $paymentItem->courseInstallment;
            $course = $paymentItem->course;

            // Retrieve the installments for this payment
            $studentsInstallments = StudentInstallment::with(['student', 'courseInstallment'])
                ->where('student_id', $payment->user_id)
                ->where('course_installment_id', $courseInstallment->id)
                ->orderBy('id', 'desc')
                ->get(['id', 'due_date']);

            // Calculate remaining installments
            $number_of_installments = $courseInstallment->number_of_installments ?? 0;
            $number_of_installments_paid = $studentsInstallments->count();
            $remaining_installments = $number_of_installments - $number_of_installments_paid;

            // Get the last due date
            $lastInstallment = $studentsInstallments->first();
            $due_date = $lastInstallment && $lastInstallment->due_date ? $lastInstallment->due_date->toDateString() : 'N/A';

            // Add data for each payment item
            $data[] = [
                $payment->id,
                $payment->user->name ?? 'N/A',
                $payment->user->email ?? 'N/A',
                strval($payment->user->phone ?? 'N/A'),
                $course->title ?? 'N/A',
                strval($payment->whatsapp_number ?? 'N/A'),
                $paymentItem->payment_type->value ?? 'N/A', // The payment type is a string, no need to access `value`
                $payment->payment_method ?? 'N/A', // Same for payment method
                // strval($payment->transfer_number ?? 'N/A'),
                // Storage::url($payment->transfer_image) ?? 'N/A',
                $payment->amount_after_coupon ?? 0,
                $payment->created_at->toDateTimeString(),
                $payment->paid_at->toDateTimeString(),
                $due_date, // Add due_date to the mapped data
                (string) $remaining_installments, // Explicitly cast to string to ensure inclusion
            ];
        }

        return $data;
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
            // 'Transfer Number',
            // 'Transfer Image',
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
