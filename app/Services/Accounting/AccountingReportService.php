<?php

namespace App\Services\Accounting;

use App\Repositories\Contracts\Accounting\EntryRepositoryInterface;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use App\Enums\AccountingCategoryType;
use App\Enums\PaymentStatusEnum;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AccountingReportService
{
    public function __construct(
        private EntryRepositoryInterface $entryRepository,
        private PaymentRepositoryInterface $paymentRepository,
        private AccountingEntryService $entryService,
        private AccountingCategoryService $categoryService
    ) {}

    public function getSummaryData(?string $fromDate, ?string $toDate, ?string $categoryId, ?string $type): array
    {
        // Get income entries through repository
        $incomeEntries = $this->entryRepository->getTotalIncome($fromDate, $toDate);

        // Get payments through repository
        $totalPayments = $this->paymentRepository->getTotalPayments($fromDate, $toDate);

        // Get expenses through repository
        $totalExpenses = $this->entryRepository->getTotalExpenses($fromDate, $toDate);

        // Calculate totals
        $totalIncome = $incomeEntries + $totalPayments;
        $netProfit = $totalIncome - $totalExpenses;        // Get counts through repositories
        $incomeEntriesCount = $this->entryRepository->getIncomeCountByDateRange($fromDate, $toDate);
        $paymentsCount = $this->paymentRepository->getPaidCountByDateRange($fromDate, $toDate);
        $expensesCount = $this->entryRepository->getExpenseCountByDateRange($fromDate, $toDate);

        return [
            'total_income' => $totalIncome,
            'income_entries' => $incomeEntries,
            'total_payments' => $totalPayments,
            'total_expenses' => $totalExpenses,
            'net_profit' => $netProfit,
            'income_entries_count' => $incomeEntriesCount,
            'payments_count' => $paymentsCount,
            'expenses_count' => $expensesCount,
            'profit_margin' => $totalIncome > 0 ? round(($netProfit / $totalIncome) * 100, 2) : 0
        ];
    }

    public function getCombinedIncomeData(?string $fromDate, ?string $toDate, ?string $categoryId = null, ?string $type = null): array
    {
        $combinedData = [];

        // Get entries through repository
        $filters = [
            'from_date' => $fromDate,
            'to_date' => $toDate,
            'category_id' => $categoryId,
            'type' => $type
        ];

        $entries = $this->entryRepository->getEntriesWithFilters($filters);

        foreach ($entries as $entry) {
            $combinedData[] = [
                'id' => 'E' . $entry['id'],
                'type' => 'Entry',
                'source' => 'Accounting Entry',
                'title' => $entry['title'],
                'description' => $entry['description'],
                'category' => $entry['category'] . ' (' . $entry['category_type'] . ')' ?? '-',
                'category_type' => $entry['category_type'] ?? '-',
                'amount' => number_format($entry['amount'], 2),
                'raw_amount' => $entry['amount'],
                'date' => $entry['date'],
                'created_at' => $entry['created_at']
            ];
        }

        // Get payments through repository (only include if type is income or no type filter)
        if (!$type || $type === 'income') {
            $paymentFilters = [
                'from_date' => $fromDate,
                'to_date' => $toDate,
                'status' => PaymentStatusEnum::Paid->value
            ];

            $payments = $this->paymentRepository->getPaymentsWithFilters($paymentFilters);
            foreach ($payments as $payment) {
                $combinedData[] = [
                    'id' => 'P' . ($payment['id'] ?? ''),
                    'type' => 'Payment',
                    'source' => ucfirst($payment['gateway'] ?? 'Unknown'),
                    'title' => 'Payment #' . ($payment['id'] ?? ''),
                    'description' => $payment['services'] ?? 'Payment received',
                    'category' => 'Payment' . ' (income)',
                    'category_type' => 'income',
                    'amount' => number_format($payment['amount_confirmed'] ?? 0, 2),
                    'raw_amount' => $payment['amount_confirmed'] ?? 0,
                    'date' => ($payment['paid_at'] ?? null) ? Carbon::parse($payment['paid_at'])->format('Y-m-d') : '-',
                    'created_at' => $payment['created_at'] ?? '-'
                ];
            }
        }

        // Sort by date (newest first)
        usort($combinedData, function ($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        return $combinedData;
    }

    public function getMonthlyTrends(?string $fromDate, ?string $toDate): array
    {
        $trends = [];
        $start = Carbon::parse($fromDate)->startOfMonth();
        $end = Carbon::parse($toDate)->endOfMonth();

        while ($start <= $end) {
            $monthStart = $start->format('Y-m-d');
            $monthEnd = $start->endOfMonth()->format('Y-m-d');

            $income = $this->entryRepository->getTotalIncome($monthStart, $monthEnd) +
                $this->paymentRepository->getTotalPayments($monthStart, $monthEnd);
            $expenses = $this->entryRepository->getTotalExpenses($monthStart, $monthEnd);

            $trends[] = [
                'month' => $start->format('M Y'),
                'income' => $income,
                'expenses' => $expenses,
                'profit' => $income - $expenses
            ];

            $start->addMonth()->startOfMonth();
        }

        return $trends;
    }

    public function getIncomeVsExpenses(?string $fromDate, ?string $toDate): array
    {
        $income = $this->entryRepository->getTotalIncome($fromDate, $toDate) +
            $this->paymentRepository->getTotalPayments($fromDate, $toDate);
        $expenses = $this->entryRepository->getTotalExpenses($fromDate, $toDate);

        return [
            'income' => $income,
            'expenses' => $expenses,
            'profit' => $income - $expenses
        ];
    }

    public function getPaymentMethodsBreakdown(?string $fromDate, ?string $toDate): array
    {
        return $this->paymentRepository->getPaymentMethodsBreakdown($fromDate, $toDate);
    }

    public function getExportData(?string $fromDate, ?string $toDate): array
    {
        return [
            'summary' => $this->getSummaryData(fromDate: $fromDate, toDate: $toDate, type: null, categoryId: null),
            'combined_data' => $this->getCombinedIncomeData($fromDate, $toDate),
            'entries' => $this->entryRepository->getEntriesForExport($fromDate, $toDate),
            'payments' => $this->paymentRepository->getPaymentsForExport($fromDate, $toDate),
            'monthly_trends' => $this->getMonthlyTrends($fromDate, $toDate)
        ];
    }
    public function getCategories()
    {
        return $this->categoryService->getAllCategories(['id', 'name', 'type']);
    }

    public function generateExcelFile(array $data, string $filename): string
    {
        // TODO: Implement Excel generation logic
        // This would use a service or export class to generate the Excel file
        // and return the file path
        return storage_path('app/exports/' . $filename);
    }

    public function getPaginatedCombinedIncomeData(
        ?string $fromDate,
        ?string $toDate,
        ?string $categoryId = null,
        ?string $type = null,
        int $start = 0,
        int $length = 25,
        string $searchValue = '',
        int $orderColumn = 6,
        string $orderDir = 'desc'
    ): array {
        // Set default values for date ranges if not provided
        $fromDate = $fromDate ?: now()->startOfMonth()->format('Y-m-d');
        $toDate = $toDate ?: now()->format('Y-m-d');

        // Get all combined data first
        $allData = $this->getCombinedIncomeData($fromDate, $toDate, $categoryId, $type);

        // Apply search filter if provided
        if (!empty($searchValue)) {
            $allData = array_filter($allData, function($item) use ($searchValue) {
                $searchFields = [
                    $item['title'] ?? '',
                    $item['description'] ?? '',
                    $item['category'] ?? '',
                    $item['source'] ?? '',
                    $item['type'] ?? '',
                    $item['amount'] ?? ''
                ];

                foreach ($searchFields as $field) {
                    if (stripos((string)$field, $searchValue) !== false) {
                        return true;
                    }
                }

                return false;
            });

            // Reindex the array after filtering
            $allData = array_values($allData);
        }

        $totalRecords = count($allData);

        // Define column mapping for sorting
        $columnMap = [
            0 => 'id',
            1 => 'type',
            2 => 'title',
            3 => 'description',
            4 => 'category',
            5 => 'raw_amount',
            6 => 'date'
        ];

        // Apply sorting
        if (isset($columnMap[$orderColumn])) {
            $sortField = $columnMap[$orderColumn];

            usort($allData, function($a, $b) use ($sortField, $orderDir) {
                $valueA = $a[$sortField] ?? '';
                $valueB = $b[$sortField] ?? '';

                // Handle numeric values
                if ($sortField === 'raw_amount') {
                    $valueA = (float) $valueA;
                    $valueB = (float) $valueB;
                }

                // Handle date values
                if ($sortField === 'date') {
                    $valueA = strtotime($valueA);
                    $valueB = strtotime($valueB);
                }

                $result = $valueA <=> $valueB;
                return $orderDir === 'desc' ? -$result : $result;
            });
        }

        // Apply pagination
        $paginatedData = array_slice($allData, $start, $length);

        // Reset array keys to ensure proper JSON encoding
        $paginatedData = array_values($paginatedData);

        return [
            'data' => $paginatedData,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords
        ];
    }
}
