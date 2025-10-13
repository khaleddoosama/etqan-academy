<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class DailySalesReportExport implements FromArray, ShouldAutoSize, WithColumnFormatting, WithEvents
{
    /**
     * @param  array<int, array<string, mixed>>  $rows
     */
    public function __construct(
        private readonly string $title,
        private readonly array $rows,
        private readonly int $totalSubscribers,
        private readonly float $totalAmount,
        private readonly string $fromDate,
        private readonly string $toDate
    ) {}

    public function array(): array
    {
        $sheetRows = [];

        $sheetRows[] = [$this->titleLabel(), '', '', '', '', ''];
        $sheetRows[] = ['تاريخ الايداع', 'اسم الكورس', 'عدد المشتركين', 'موظف المبيعات', 'ملاحظات', 'المبلغ'];

        foreach ($this->rows as $row) {
            /** @var \Carbon\CarbonInterface|null $date */
            $date = $row['date'] ?? null;
            $sheetRows[] = [
                $date ? ExcelDate::dateTimeToExcel($date) : '',
                $row['course'] ?? '',
                $row['subscribers'] ?? '',
                $row['sales_employee'] ?? '',
                $row['notes'] ?? '',
                isset($row['amount']) ? round((float) $row['amount'], 2) : null,
            ];
        }

        $sheetRows[] = ['', '', '', '', '', ''];
        $sheetRows[] = ['', '', '', '', '', ''];
        $sheetRows[] = [
            'Total',
            '',
            $this->totalSubscribers,
            '',
            '',
            round($this->totalAmount, 2),
        ];

        return $sheetRows;
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_DATE_YYYYMMDD2,
            'F' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();


                $sheet->setRightToLeft(true);
                $sheet->mergeCells('A1:F1');
                $sheet->setCellValue('A1', $this->titleLabel());
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 16,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(28);

                $headerRange = 'A2:F2';
                $sheet->getStyle($headerRange)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '5b9bd5'],
                    ],
                ]);
                $sheet->getStyle($headerRange)->getBorders()->getBottom()->setBorderStyle(Border::BORDER_MEDIUM);

                $dataRowCount = count($this->rows);
                $firstDataRow = 3;
                $lastDataRow = $dataRowCount > 0 ? $firstDataRow + $dataRowCount - 1 : $firstDataRow - 1;

                if ($dataRowCount > 0) {
                    $dataRange = "A2:F{$lastDataRow}";
                    $sheet->getStyle($dataRange)->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => 'B0BEC5'],
                            ],
                        ],
                        'font' => [
                            'bold' => true,
                            'size' => 14,
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                    ]);
                    $sheet->getStyle("C{$firstDataRow}:C{$lastDataRow}")
                        ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("F{$firstDataRow}:F{$lastDataRow}")
                        ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                }

                $totalRowIndex = $dataRowCount + 5;
                $totalRange = "A{$totalRowIndex}:F{$totalRowIndex}";
                $sheet->getStyle($totalRange)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'outline' => [
                            'borderStyle' => Border::BORDER_MEDIUM,
                            'color' => ['rgb' => 'FF9800'],
                        ],
                    ],
                ]);
                $sheet->getStyle("C{$totalRowIndex}")
                    ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("F{$totalRowIndex}")
                    ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            },
        ];
    }

    public function getFilename(): string
    {
        return sprintf('daily_sales_report_%s_to_%s.xlsx', $this->fromDate, $this->toDate);
    }

    private function titleLabel(): string
    {
        if ($this->fromDate === $this->toDate) {
            return sprintf('%s (%s)', $this->title, $this->fromDate);
        }

        return sprintf('%s (%s - %s)', $this->title, $this->fromDate, $this->toDate);
    }
}
