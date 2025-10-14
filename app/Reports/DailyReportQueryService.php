<?php

namespace App\Reports;

use App\Enums\AccountingCategoryType;
use App\Enums\PaymentStatusEnum;
use App\Enums\PaymentType;
use App\Models\Accounting\Entry;
use App\Models\Payment;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

final class DailyReportQueryService
{
    public function getData(?string $fromDate, ?string $toDate): DailyReportData
    {
        [$from, $to] = $this->resolveDateRange($fromDate, $toDate);

        $payments = $this->collectPayments($from, $to);
        $incomeEntries = $this->collectEntries($from, $to, AccountingCategoryType::INCOME);
        $expenseEntries = $this->collectEntries($from, $to, AccountingCategoryType::EXPENSE);

        return new DailyReportData($from, $to, $payments, $incomeEntries, $expenseEntries);
    }

    /**
     * @return array<int, DailyReportPayment>
     */
    private function collectPayments(CarbonImmutable $from, CarbonImmutable $to): array
    {
        /** @var \Illuminate\Support\Collection<int, Payment> $payments */
        $payments = Payment::query()
            ->with([
                'paymentItems.course:id,title',
                'paymentItems.packagePlan:id,title',
                'paymentItems.courseInstallment',
            ])
            ->where('status', PaymentStatusEnum::Paid->value)
            ->whereNotNull('paid_at')
            ->whereBetween('paid_at', [$from->startOfDay(), $to->endOfDay()])
            ->orderBy('paid_at')
            ->get();

        if ($payments->isEmpty()) {
            return [];
        }

        $installmentBaseline = $this->baselineInstallmentCounts($payments, $from);
        $installmentTracker = $installmentBaseline;

        $result = [];

        foreach ($payments as $payment) {
            $paidAt = $payment->paid_at ?? $payment->created_at;
            $date = $this->asImmutable($paidAt ?? now());

            $items = $payment->paymentItems->map(function ($item) use ($payment) {
                $rawAmount = $item->amount;

                if ($rawAmount === null) {
                    $itemCount = max(1, $payment->paymentItems->count());
                    $rawAmount = ($payment->amount_confirmed ?? 0) / $itemCount;
                }

                $paymentType = $item->payment_type instanceof PaymentType
                    ? $item->payment_type
                    : PaymentType::from($item->payment_type ?? PaymentType::CASH->value);

                return new DailyReportPaymentItem(
                    title: $item->serviceTitle ?? 'غير محدد',
                    type: $paymentType,
                    courseId: $item->course_id ? (int) $item->course_id : null
                );
            })->all();

            $userId = (int) ($payment->user_id ?? 0);
            $isInstallment = (int) $payment->coupon_id === 8;
            $previousCount = $installmentTracker[$userId] ?? 0;
            $hasPreviousInstallment = $isInstallment && $previousCount > 0;

            if ($isInstallment) {
                $installmentTracker[$userId] = $previousCount + 1;
            }

            $result[] = new DailyReportPayment(
                id: (int) $payment->id,
                userId: $userId,
                couponId: $payment->coupon_id !== null ? (int) $payment->coupon_id : null,
                date: $date,
                amount: (float) $payment->amount_confirmed,
                isInstallment: $isInstallment,
                isSubsequentInstallment: $hasPreviousInstallment,
                items: $items
            );
        }

        return $result;
    }

    /**
     * @return array<int, DailyReportEntry>
     */
    private function collectEntries(
        CarbonImmutable $from,
        CarbonImmutable $to,
        AccountingCategoryType $type
    ): array {
        /** @var Collection<int, Entry> $entries */
        $entries = Entry::query()
            ->with('category:id,name,type')
            ->whereHas('category', fn ($query) => $query->where('type', $type->value))
            ->whereBetween('transaction_date', [$from->toDateString(), $to->toDateString()])
            ->orderBy('transaction_date')
            ->get();

        if ($entries->isEmpty()) {
            return [];
        }

        return $entries
            ->map(fn (Entry $entry) => new DailyReportEntry(
                id: (int) $entry->id,
                date: CarbonImmutable::createFromInterface($entry->transaction_date),
                title: $entry->title,
                description: $entry->description,
                categoryName: $entry->category?->name ?? 'غير محدد',
                amount: (float) $entry->amount,
                type: $type
            ))
            ->all();
    }

    /**
     * @param Collection<int, Payment> $payments
     * @return array<int, int>
     */
    private function baselineInstallmentCounts(Collection $payments, CarbonImmutable $from): array
    {
        $userIds = $payments
            ->pluck('user_id')
            ->filter(fn ($id) => $id !== null)
            ->unique()
            ->all();

        if (empty($userIds)) {
            return [];
        }

        return Payment::query()
            ->selectRaw('user_id, COUNT(*) as aggregate_count')
            ->where('status', PaymentStatusEnum::Paid->value)
            ->where('coupon_id', 8)
            ->whereIn('user_id', $userIds)
            ->whereDate('paid_at', '<', $from->toDateString())
            ->groupBy('user_id')
            ->pluck('aggregate_count', 'user_id')
            ->map(fn ($count) => (int) $count)
            ->all();
    }

    /**
     * @return array{0: CarbonImmutable, 1: CarbonImmutable}
     */
    private function resolveDateRange(?string $fromDate, ?string $toDate): array
    {
        $today = CarbonImmutable::now()->startOfDay();

        $from = $fromDate
            ? CarbonImmutable::parse($fromDate)->startOfDay()
            : $today;

        $to = $toDate
            ? CarbonImmutable::parse($toDate)->endOfDay()
            : $from->copy()->endOfDay();

        if ($from->greaterThan($to)) {
            [$from, $to] = [$to->startOfDay(), $from->endOfDay()];
        }

        return [$from, $to];
    }

    private function asImmutable(?CarbonInterface $value): CarbonImmutable
    {
        $candidate = $value ?? now();

        return $candidate instanceof CarbonImmutable
            ? $candidate
            : CarbonImmutable::createFromInterface($candidate);
    }
}

final class DailyReportData
{
    /**
     * @param array<int, DailyReportPayment> $payments
     * @param array<int, DailyReportEntry> $incomeEntries
     * @param array<int, DailyReportEntry> $expenseEntries
     */
    public function __construct(
        public readonly CarbonImmutable $fromDate,
        public readonly CarbonImmutable $toDate,
        public readonly array $payments,
        public readonly array $incomeEntries,
        public readonly array $expenseEntries,
    ) {}
}

final class DailyReportPayment
{
    /**
     * @param array<int, DailyReportPaymentItem> $items
     */
    public function __construct(
        public readonly int $id,
        public readonly int $userId,
        public readonly ?int $couponId,
        public readonly CarbonImmutable $date,
        public readonly float $amount,
        public readonly bool $isInstallment,
        public readonly bool $isSubsequentInstallment,
        public readonly array $items,
    ) {}
}

final class DailyReportPaymentItem
{
    public function __construct(
        public readonly string $title,
        public readonly PaymentType $type,
        public readonly ?int $courseId,
    ) {}
}

final class DailyReportEntry
{
    public function __construct(
        public readonly int $id,
        public readonly CarbonImmutable $date,
        public readonly string $title,
        public readonly ?string $description,
        public readonly string $categoryName,
        public readonly float $amount,
        public readonly AccountingCategoryType $type,
    ) {}
}
