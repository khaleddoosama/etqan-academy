<?php

namespace App\Exports;

use App\Enums\PaymentType;
use App\Enums\Status;
use App\Models\PaymentDetails;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SummaryPaymentsExport implements FromCollection, WithHeadings, WithStyles
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
        Log::info('Summary Payments Exported: ' . now()->toDateString());

        $summaryData = [];

        // Loop through each day of the current week
        for ($day = $this->startOfWeek; $day->lt($this->endOfWeek); $day->addDay()) {
            $date = $day->toDateString();

            $dailyData = [
                'day' => $day->format('l'),
                'date' => $date,

                'total_subscribers' => (string) (PaymentDetails::whereDate('approved_at', $date)->where('status', Status::APPROVED->value)->count() ?: 0),
                'total_income' => (string) (PaymentDetails::whereDate('approved_at', $date)->where('status', Status::APPROVED->value)->sum('amount') ?: 0),

                'cash_subscribers' => (string) (PaymentDetails::where('payment_type', PaymentType::CASH->value)->whereDate('approved_at', $date)->where('status', Status::APPROVED->value)->count() ?: 0),
                'cash_income' => (string) (PaymentDetails::where('payment_type', PaymentType::CASH->value)->whereDate('approved_at', $date)->where('status', Status::APPROVED->value)->sum('amount') ?: 0),

                'installment_subscribers' => (string) (PaymentDetails::where('payment_type', PaymentType::INSTALLMENT->value)->whereDate('approved_at', $date)->where('status', Status::APPROVED->value)->count() ?: 0),
                'installment_income' => (string) (PaymentDetails::where('payment_type', PaymentType::INSTALLMENT->value)->whereDate('approved_at', $date)->where('status', Status::APPROVED->value)->sum('amount') ?: 0),

                'super_graphic_subscribers' => (string) (PaymentDetails::whereHas('courseInstallment.course', function ($query) {
                    $query->where('title', 'LIKE', '%سوبر جرافيك%');
                })->whereDate('approved_at', $date)->where('status', Status::APPROVED->value)->count() ?: 0),
                'mini_graphic_subscribers' => (string) (PaymentDetails::whereHas('courseInstallment.course', function ($query) {
                    $query->where('title', 'LIKE', '%ميني جرافيك%');
                })->whereDate('approved_at', $date)->where('status', Status::APPROVED->value)->count() ?: 0),
            ];


            $summaryData[] = $dailyData;
        }

        // حساب الإجمالي الأسبوعي
        $weeklySummary = [
            'day' => 'إجمالي الأسبوع',
            'date' => '',
            'total_subscribers' => (string) (array_sum(array_column($summaryData, 'total_subscribers')) ?: 0),
            'total_income' => (string) (array_sum(array_column($summaryData, 'total_income')) ?: 0),
            'cash_subscribers' => (string) (array_sum(array_column($summaryData, 'cash_subscribers')) ?: 0),
            'cash_income' => (string) (array_sum(array_column($summaryData, 'cash_income')) ?: 0),
            'installment_subscribers' => (string) (array_sum(array_column($summaryData, 'installment_subscribers')) ?: 0),
            'installment_income' => (string) (array_sum(array_column($summaryData, 'installment_income')) ?: 0),
            'super_graphic_subscribers' => (string) (array_sum(array_column($summaryData, 'super_graphic_subscribers')) ?: 0),
            'mini_graphic_subscribers' => (string) (array_sum(array_column($summaryData, 'mini_graphic_subscribers')) ?: 0),
        ];


        $summaryData[] = $weeklySummary;

        return collect($summaryData);
    }

    public function headings(): array
    {
        return [
            'اليوم',
            'التاريخ',
            'إجمالي عدد المشتركين',
            'إجمالي الدخل',
            'عدد المشتركين كاش',
            'إجمالي الكاش',
            'عدد المشتركين تقسيط',
            'إجمالي التقسيط',
            'عدد مشتركين السوبر جرافيك',
            'عدد مشتركين الميني جرافيك',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:J1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => 'solid',
                'startColor' => ['rgb' => '3F51B5'], // blue background
            ],
        ]);

        foreach (range('A', 'J') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // تلوين صف إجمالي الأسبوع
        foreach ($sheet->getRowIterator() as $row) {
            $cellValue = $sheet->getCell('A' . $row->getRowIndex())->getValue(); // الحصول على قيمة العمود A
            if ($cellValue === 'إجمالي الأسبوع') {
                $sheet->getStyle('A' . $row->getRowIndex() . ':J' . $row->getRowIndex())->applyFromArray([
                    'fill' => [
                        'fillType' => 'solid',
                        'startColor' => ['rgb' => 'FFC107'], // لون أصفر مميز
                    ],
                    'font' => [
                        'bold' => true, // خط عريض
                    ],
                ]);
            }
        }

        // ضبط تنسيق الأعمدة الرقمية لإظهار 0
        $sheet->getStyle('C2:J100')->getNumberFormat()->setFormatCode('0'); // عرض القيم الصفرية

        return $sheet;
    }
}
