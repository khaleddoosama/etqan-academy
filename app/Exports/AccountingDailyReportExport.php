<?php

namespace App\Exports;

use App\Reports\DailyReportResult;
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

class AccountingDailyReportExport implements FromArray, ShouldAutoSize, WithColumnFormatting, WithEvents
{
    private const HEADER = ['التاريخ', 'الحساب', 'البيان', 'المبلغ'];

    /**
     * @var array<string, array<string, int>>
     */
    private array $sectionsMeta = [];

    public function __construct(
        private readonly DailyReportResult $result
    ) {}

    public function array(): array
    {
        $rows = [];

        // Income section
        $this->sectionsMeta['income']['label'] = 'الإيرادات';
        $this->sectionsMeta['income']['title'] = count($rows) + 1;
        $rows[] = [$this->sectionsMeta['income']['label'], '', '', ''];

        $this->sectionsMeta['income']['header'] = count($rows) + 1;
        $rows[] = self::HEADER;

        $incomeDataStart = count($rows) + 1;
        foreach ($this->result->incomeRows as $row) {
            $rows[] = [
                ExcelDate::dateTimeToExcel($row->date),
                $row->account,
                $row->description,
                round($row->amount, 2),
            ];
        }
        $incomeDataEnd = count($rows);
        $this->sectionsMeta['income']['data_start'] = $incomeDataStart;
        $this->sectionsMeta['income']['data_end'] = $incomeDataEnd >= $incomeDataStart ? $incomeDataEnd : $incomeDataStart - 1;

        $this->sectionsMeta['income']['total'] = count($rows) + 1;
        $rows[] = ['الإجمالي', '', '', round($this->result->incomeTotal, 2)];

        // Spacer row
        $rows[] = ['', '', '', ''];

        // Expense section
        $this->sectionsMeta['expense']['label'] = 'المصروفات';
        $this->sectionsMeta['expense']['title'] = count($rows) + 1;
        $rows[] = [$this->sectionsMeta['expense']['label'], '', '', ''];

        $this->sectionsMeta['expense']['header'] = count($rows) + 1;
        $rows[] = self::HEADER;

        $expenseDataStart = count($rows) + 1;
        foreach ($this->result->expenseRows as $row) {
            $rows[] = [
                ExcelDate::dateTimeToExcel($row->date),
                $row->account,
                $row->description,
                round($row->amount, 2),
            ];
        }
        $expenseDataEnd = count($rows);
        $this->sectionsMeta['expense']['data_start'] = $expenseDataStart;
        $this->sectionsMeta['expense']['data_end'] = $expenseDataEnd >= $expenseDataStart ? $expenseDataEnd : $expenseDataStart - 1;

        $this->sectionsMeta['expense']['total'] = count($rows) + 1;
        $rows[] = ['الإجمالي', '', '', round($this->result->expenseTotal, 2)];

        return $rows;
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_DATE_YYYYMMDD2,
            'D' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event): void {
                $sheet = $event->sheet->getDelegate();
                $sheet->setRightToLeft(true);

                foreach ($this->sectionsMeta as $meta) {
                    $this->styleSection($sheet, $meta);
                }
            },
        ];
    }

    public function getFilename(): string
    {
        $from = $this->result->fromDate->format('Y-m-d');
        $to = $this->result->toDate->format('Y-m-d');

        if ($from === $to) {
            return sprintf('daily_report_%s.xlsx', $from);
        }

        return sprintf('daily_report_%s_to_%s.xlsx', $from, $to);
    }

    /**
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet
     * @param array<string, int|string> $meta
     */
    private function styleSection(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet, array $meta): void
    {
        /** @var string $label */
        $label = $meta['label'] ?? '';
        $titleRow = (int) ($meta['title'] ?? 0);
        $headerRow = (int) ($meta['header'] ?? 0);
        $dataStart = (int) ($meta['data_start'] ?? 0);
        $dataEnd = (int) ($meta['data_end'] ?? 0);
        $totalRow = (int) ($meta['total'] ?? 0);

        if ($titleRow > 0) {
            $sheet->mergeCells(sprintf('A%d:D%d', $titleRow, $titleRow));
            $sheet->setCellValue(sprintf('A%d', $titleRow), $label);
            $sheet->getStyle(sprintf('A%d:D%d', $titleRow, $titleRow))->applyFromArray([
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);
        }

        if ($headerRow > 0) {
            $headerRange = sprintf('A%d:D%d', $headerRow, $headerRow);
            $sheet->getStyle($headerRange)->applyFromArray([
                'font' => ['bold' => true],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'D0E3F8'],
                ],
                'borders' => [
                    'bottom' => ['borderStyle' => Border::BORDER_MEDIUM],
                ],
            ]);
        }

        if ($dataStart > 0 && $dataStart <= $dataEnd) {
            $dataRange = sprintf('A%d:D%d', $dataStart, $dataEnd);
            $sheet->getStyle($dataRange)->applyFromArray([
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                ],
            ]);
        }

        if ($totalRow > 0) {
            $totalRange = sprintf('A%d:D%d', $totalRow, $totalRow);
            $sheet->getStyle($totalRange)->applyFromArray([
                'font' => ['bold' => true],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'outline' => ['borderStyle' => Border::BORDER_THICK],
                ],
            ]);
        }
    }
}
