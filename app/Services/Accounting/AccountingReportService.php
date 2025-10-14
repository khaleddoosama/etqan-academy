<?php

namespace App\Services\Accounting;

use App\Enums\AccountingCategoryType;
use App\Enums\PaymentStatusEnum;
use App\Enums\PaymentType;
use App\Exports\DailySalesReportExport;
use App\Models\Payment;
use App\Models\PaymentItems;
use App\Repositories\Contracts\Accounting\EntryRepositoryInterface;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

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
            Log::info('Processing entry', ['entry' => $entry['amount']]);
            $combinedData[] = [
                'id' => 'E' . $entry['id'],
                'type' => 'Entry',
                'source' => 'Accounting Entry',
                'title' => $entry['title'],
                'description' => $entry['description'],
                'category' => $entry['category'] . ' (' . $entry['category_type'] . ')' ?? '-',
                'category_type' => $entry['category_type'] ?? '-',
                'amount' => $entry['amount'],
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

    public function makeDailySalesReportExport(?string $fromDate, ?string $toDate): DailySalesReportExport
    {
        $reportData = $this->getDailySalesReportData($fromDate, $toDate);

        return new DailySalesReportExport(
            title: $reportData['title'],
            rows: $reportData['rows'],
            totalSubscribers: $reportData['totals']['subscribers'],
            totalAmount: $reportData['totals']['amount'],
            fromDate: $reportData['from'],
            toDate: $reportData['to']
        );
    }

    public function getDailySalesReportData(?string $fromDate, ?string $toDate): array
    {
        [$resolvedFrom, $resolvedTo] = $this->resolveDateRange($fromDate, $toDate);

        $payments = $this->paymentRepository->getPaymentsForDailySalesReport($resolvedFrom, $resolvedTo);

        $groupedRows = [];
        $totalSubscribers = 0;
        $totalAmount = 0.0;

        foreach ($payments as $payment) {
            /** @var PaymentItems $item */
            $salesEmployee = $this->resolveCouponSalesEmployee($payment);
            foreach ($payment->paymentItems as $item) {
                $date = $payment->paid_at ?? $payment->created_at;
                $dateKey = $date ? Carbon::parse($date)->format('Y-m-d') : 'غير محدد';
                $courseName = $item->serviceTitle
                    ?? $item->course?->title
                    ?? $item->courseInstallment?->course?->title
                    ?? $item->packagePlan?->title
                    ?? $item->packagePlan?->package?->title
                    ?? 'غير محدد';

                $key = $dateKey . '|' . $courseName . '|' . $payment->coupon_id;

                $amount = (float) $payment->amount_confirmed;

                $notes = $this->resolvePaymentNotes($payment, $item->course_id);

                if (!isset($groupedRows[$key])) {
                    $groupedRows[$key] = [
                        'date' => Carbon::parse($date),
                        'course' => $courseName,
                        'subscribers' => 0,
                        'sales_employee' => $salesEmployee,
                        'notes' => $notes,
                        'amount' => 0,
                    ];
                } else {
                    $groupedRows[$key]['notes'] .= "; $notes";
                }

                $groupedRows[$key]['subscribers'] += 1;
                $groupedRows[$key]['amount'] += $amount;
            }
        }

        $rows = array_values($groupedRows);

        // حساب الإجماليات
        foreach ($rows as &$row) {
            $totalSubscribers += $row['subscribers'];
            $totalAmount += $row['amount'];
            // remove last ; from notes if exists
            $row['notes'] = rtrim(string: $row['notes'], characters: "; ");
        }

        // ترتيب النتائج
        usort($rows, function (array $a, array $b) {
            $timestampA = $a['date'] ? $a['date']->timestamp : 0;
            $timestampB = $b['date'] ? $b['date']->timestamp : 0;

            if ($timestampA === $timestampB) {
                return strcmp((string) $a['course'], (string) $b['course']);
            }

            return $timestampA <=> $timestampB;
        });

        return [
            'title' => 'جدول مبيعات يوميه',
            'from' => $resolvedFrom,
            'to' => $resolvedTo,
            'rows' => $rows,
            'totals' => [
                'subscribers' => $totalSubscribers,
                'amount' => round($totalAmount, 2),
            ],
        ];
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
            $allData = array_filter($allData, function ($item) use ($searchValue) {
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

            usort($allData, function ($a, $b) use ($sortField, $orderDir) {
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

    private function resolveDateRange(?string $fromDate, ?string $toDate): array
    {
        $from = $fromDate ? Carbon::parse($fromDate) : now()->startOfMonth();
        $to = $toDate ? Carbon::parse($toDate) : now();

        if ($from->greaterThan($to)) {
            [$from, $to] = [$to->copy(), $from->copy()];
        }

        return [$from->toDateString(), $to->toDateString()];
    }

    private function resolveCouponSalesEmployee(Payment $payment): string
    {
        $couponMap = [
            8 => 'قسط',
            7 => 'كتب',
            6 => 'عبدالله فتحي',
            4 => 'محمد وليد',
            5 => 'منار خالد',
        ];
        $couponId = $payment->coupon_id ?? null;
        $salesEmployee = $couponId && isset($couponMap[$couponId])
            ? $couponMap[$couponId] : '';

        return $salesEmployee;
    }

    private function resolvePaymentNotes(Payment $payment, ?int $courseId): string
    {

        // check if there another payment with the same user_id and course_id before this day
        $anotherPayment = Payment::where('user_id', $payment->user_id)
            ->whereHas('paymentItems', function ($query) use ($courseId) {
                $query->where('course_id', $courseId);
            })
            ->whereDate('paid_at', '<', Carbon::parse($payment->paid_at)->toDateString())
            ->where('status', PaymentStatusEnum::Paid->value)
            ->where('id', '!=', $payment->id)
            ->where('coupon_id', '!=', 8) // not قسط
            ->first();

        return $anotherPayment ? 'باقي قسط' : '';

    }
}
