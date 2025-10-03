<?php

namespace App\Repositories\Contracts;

use Carbon\Carbon;
use Illuminate\Support\Collection;

interface PaymentRepositoryInterface extends BaseRepositoryInterface
{
    public function getWeeklyPaidCashPayments(string $startOfWeek, string $endOfWeek): Collection;
    public function getWeeklyPaidInstallmetPayments(string $startOfWeek, string $endOfWeek): Collection;
    public function getDailyPaidSubscriberCount(string $date): int;
    public function getDailyPaidIncome(string $date): float;
    public function getDailySubscriberCountByType(string $date, string $type): int;
    public function getDailyIncomeByType(string $date, string $type): float;
    public function canUserPay($userId): bool;
    
    // New methods for reporting
    public function getTotalPayments(?string $fromDate, ?string $toDate): float;
    public function getPaidCountByDateRange(?string $fromDate, ?string $toDate): int;
    public function getPaymentsWithFilters(array $filters): array;
    public function getPaymentsBreakdown(?string $fromDate, ?string $toDate): array;
    public function getPaymentMethodsBreakdown(?string $fromDate, ?string $toDate): array;
    public function getPaymentsForExport(?string $fromDate, ?string $toDate): array;
}
