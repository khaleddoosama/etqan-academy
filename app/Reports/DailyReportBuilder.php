<?php

namespace App\Reports;

use Carbon\CarbonImmutable;

final class DailyReportBuilder
{
    private const STATUS_STANDARD = 'standard';
    private const STATUS_DOWN_PAYMENT = 'down_payment';
    private const STATUS_REMAINING = 'remaining';

    private const PAYMENT_STATUS_ORDER = [
        self::STATUS_STANDARD,
        self::STATUS_DOWN_PAYMENT,
        self::STATUS_REMAINING,
    ];

    public function build(DailyReportData $data): DailyReportResult
    {
        $incomeRows = $this->buildIncomeRows($data);
        $expenseRows = $this->buildExpenseRows($data->expenseEntries);

        $incomeTotal = $this->sumRows($incomeRows);
        $expenseTotal = $this->sumRows($expenseRows);

        return new DailyReportResult(
            fromDate: $data->fromDate,
            toDate: $data->toDate,
            incomeRows: $incomeRows,
            incomeTotal: $incomeTotal,
            expenseRows: $expenseRows,
            expenseTotal: $expenseTotal,
        );
    }

    private function buildIncomeRows(DailyReportData $data): array
    {
        $groups = [];
        $installmentHistory = [];

        foreach ($data->payments as $payment) {
            $dateKey = $payment->date->format('Y-m-d');
            $account = $this->resolvePaymentAccount($payment);
            $groupKey = $this->groupKey($dateKey, $account);

            if (!isset($groups[$groupKey])) {
                $groups[$groupKey] = $this->makeEmptyIncomeGroup($payment->date, $account);
            }

            foreach ($payment->items as $item) {
                $courseKey = $this->normalizeKey($item->title) ?: '__empty__';
                $itemStatus = $this->determineItemStatus($payment, $item, $account, $installmentHistory);
                $display = trim($item->title) !== '' ? trim($item->title) : 'غير محدد';

                if (!isset($groups[$groupKey]['courses'][$courseKey])) {
                    $groups[$groupKey]['courses'][$courseKey] = [
                        'display' => $display,
                        'statuses' => [],
                    ];
                    $groups[$groupKey]['course_order'][] = $courseKey;
                }

                if (!isset($groups[$groupKey]['courses'][$courseKey]['statuses'][$itemStatus])) {
                    $groups[$groupKey]['courses'][$courseKey]['statuses'][$itemStatus] = [
                        'count' => 0,
                        'total' => 0.0,
                        'price_groups' => [],
                    ];
                }

                $statusBucket = &$groups[$groupKey]['courses'][$courseKey]['statuses'][$itemStatus];
                $statusBucket['count']++;
                $statusBucket['total'] += $payment->amount;

                $priceKey = $this->priceKey($payment->amount);

                if (!isset($statusBucket['price_groups'][$priceKey])) {
                    $statusBucket['price_groups'][$priceKey] = [
                        'price' => $payment->amount,
                        'count' => 0,
                    ];
                }

                $statusBucket['price_groups'][$priceKey]['count']++;

                $groups[$groupKey]['total'] += $payment->amount;
                unset($statusBucket);
            }
        }

        foreach ($data->incomeEntries as $entry) {
            $dateKey = $entry->date->format('Y-m-d');
            $account = 'قيود';
            $groupKey = $this->groupKey($dateKey, $account);

            if (!isset($groups[$groupKey])) {
                $groups[$groupKey] = $this->makeEmptyIncomeGroup($entry->date, $account);
            }

            $entryKey = $this->normalizeKey($entry->title) ?: '__entry__';
            $display = $this->composeEntryDisplay($entry);

            if (!isset($groups[$groupKey]['entries'][$entryKey])) {
                $groups[$groupKey]['entries'][$entryKey] = [
                    'display' => $display,
                    'count' => 0,
                    'total' => 0.0,
                ];
                $groups[$groupKey]['entry_order'][] = $entryKey;
            }

            $groups[$groupKey]['entries'][$entryKey]['count']++;
            $groups[$groupKey]['entries'][$entryKey]['total'] += $entry->amount;
            $groups[$groupKey]['total'] += $entry->amount;
        }

        $rows = [];

        foreach ($groups as $group) {
            $phrases = [];

            foreach ($group['course_order'] as $courseKey) {
                $course = $group['courses'][$courseKey];

                foreach (self::PAYMENT_STATUS_ORDER as $status) {
                    if (!isset($course['statuses'][$status])) {
                        continue;
                    }

                    $stats = $course['statuses'][$status];
                    $phrases[] = $this->renderCoursePhrase(
                        $course['display'],
                        $stats,
                        $status
                    );
                }
            }

            foreach ($group['entry_order'] as $entryKey) {
                $entry = $group['entries'][$entryKey];

                if ($entry['count'] === 0) {
                    continue;
                }

                $phrases[] = $this->renderEntryPhrase(
                    $entry['display'],
                    $entry['total']
                );
            }

            $description = implode(' + ', array_filter($phrases));

            if ($description === '') {
                continue;
            }

            $rows[] = new DailyReportRow(
                date: $group['date'],
                account: $group['account'],
                description: $description,
                amount: $group['total'],
                source: 'income-group'
            );
        }

        usort($rows, $this->dateSorter(...));

        return $rows;
    }

    /**
     * @param array<int, DailyReportEntry> $entries
     * @return array<int, DailyReportRow>
     */
    private function buildExpenseRows(array $entries): array
    {
        $rows = array_map(
            fn (DailyReportEntry $entry) => new DailyReportRow(
                date: $entry->date,
                account: $entry->categoryName,
                description: $this->renderEntryPhrase($this->composeEntryDisplay($entry), $entry->amount),
                amount: $entry->amount,
                source: 'entry'
            ),
            $entries
        );

        usort($rows, $this->dateSorter(...));

        return $rows;
    }

    private function resolvePaymentAccount(DailyReportPayment $payment): string
    {
        return (int) ($payment->couponId ?? 0) === 7
            ? 'مبيعات كتب'
            : 'مبيعات كورسات';
    }

    /**
     * @param array{count:int,total:float,price_groups:array<string,array{price:float,count:int}>} $statusData
     */
    private function renderCoursePhrase(string $title, array $statusData, string $status): string
    {
        $title = $title !== '' ? $title : 'غير محدد';
        $count = (int) ($statusData['count'] ?? 0);
        $priceExpression = $this->formatPriceGroups($statusData['price_groups'] ?? []);

        $base = sprintf('اشتراك عدد %s %s', $count, $title);
        $statusLabel = $this->statusLabel($status);

        if ($statusLabel) {
            $base .= sprintf(' (%s)', $statusLabel);
        }

        if ($priceExpression !== '') {
            $base .= sprintf(' (%s)', $priceExpression);
        }

        return $base;
    }

    private function renderEntryPhrase(string $title, float $total): string
    {
        return sprintf('%s (%s)', $title, $this->formatNumber($total));
    }

    private function composeEntryDisplay(DailyReportEntry $entry): string
    {
        $title = trim((string) $entry->title);

        if ($title === '') {
            $title = 'غير محدد';
        }

        $details = trim((string) $entry->description);

        if ($details !== '') {
            $title .= ' - ' . $details;
        }

        return $title;
    }

    private function formatNumber(float $value): string
    {
        $rounded = round($value, 2);

        if (abs($rounded - round($rounded)) < 0.00001) {
            return (string) (int) round($rounded);
        }

        $formatted = number_format($rounded, 2, '.', '');

        return rtrim(rtrim($formatted, '0'), '.');
    }

    /**
     * @param array<string, array{price: float, count: int}> $priceGroups
     */
    private function formatPriceGroups(array $priceGroups): string
    {
        if ($priceGroups === []) {
            return '';
        }

        $parts = [];

        foreach ($priceGroups as $group) {
            $count = (int) ($group['count'] ?? 0);
            $price = $this->formatNumber((float) ($group['price'] ?? 0.0));
            $parts[] = sprintf('%d*%s', $count, $price);
        }

        return implode(' + ', $parts);
    }

    /**
     * @param array<int, DailyReportRow> $rows
     */
    private function sumRows(array $rows): float
    {
        return array_reduce(
            $rows,
            static fn (float $carry, DailyReportRow $row) => $carry + $row->amount,
            0.0
        );
    }

    private function dateSorter(DailyReportRow $left, DailyReportRow $right): int
    {
        return $left->date <=> $right->date;
    }

    private function groupKey(string $date, string $account): string
    {
        return $date . '|' . $account;
    }

    private function makeEmptyIncomeGroup(CarbonImmutable $date, string $account): array
    {
        return [
            'date' => $date,
            'account' => $account,
            'total' => 0.0,
            'courses' => [],
            'course_order' => [],
            'entries' => [],
            'entry_order' => [],
        ];
    }

    private function normalizeKey(string $value): string
    {
        $normalized = trim($value);

        if ($normalized === '') {
            return '';
        }

        return function_exists('mb_strtolower')
            ? mb_strtolower($normalized)
            : strtolower($normalized);
    }

    private function priceKey(float $value): string
    {
        return number_format($value, 6, '.', '');
    }

    /**
     * @param array<int, array<int, bool>> $history
     */
    private function determineItemStatus(
        DailyReportPayment $payment,
        DailyReportPaymentItem $item,
        string $account,
        array &$history
    ): string {
        if ($account !== 'مبيعات كورسات') {
            return self::STATUS_STANDARD;
        }

        $userId = $payment->userId;
        $courseId = $item->courseId;

        if ($payment->couponId === 8) {
            if ($userId > 0 && $courseId !== null) {
                $history[$userId][$courseId] = true;
            }

            return self::STATUS_DOWN_PAYMENT;
        }

        if ($userId > 0 && $courseId !== null && isset($history[$userId][$courseId])) {
            return self::STATUS_REMAINING;
        }

        return self::STATUS_STANDARD;
    }

    private function statusLabel(string $status): ?string
    {
        return match ($status) {
            self::STATUS_DOWN_PAYMENT => 'مقدم قسط',
            self::STATUS_REMAINING => 'باقي قسط',
            default => null,
        };
    }
}

final class DailyReportResult
{
    /**
     * @param array<int, DailyReportRow> $incomeRows
     * @param array<int, DailyReportRow> $expenseRows
     */
    public function __construct(
        public readonly CarbonImmutable $fromDate,
        public readonly CarbonImmutable $toDate,
        public readonly array $incomeRows,
        public readonly float $incomeTotal,
        public readonly array $expenseRows,
        public readonly float $expenseTotal,
    ) {}
}

final class DailyReportRow
{
    public function __construct(
        public readonly CarbonImmutable $date,
        public readonly string $account,
        public readonly string $description,
        public readonly float $amount,
        public readonly string $source,
    ) {}
}
