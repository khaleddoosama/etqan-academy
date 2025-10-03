<?php

namespace App\Repositories\Contracts\Accounting;

use App\Models\Accounting\Entry;
use App\Repositories\Contracts\BaseRepositoryInterface;
use App\Enums\AccountingCategoryType;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

/**
 * @extends BaseRepositoryInterface<Entry>
 */
interface EntryRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get entries with filters for DataTable
     */
    public function getForDataTable(Request $request): LengthAwarePaginator;

    /**
     * Get entries by type
     */
    public function getByType(AccountingCategoryType $type): Collection;

    /**
     * Get entries by category
     */
    public function getByCategory(int $categoryId): Collection;


    /**
     * Get entries by date range
     */
    public function getByDateRange(?string $from, ?string $to): Collection;

    /**
     * Get income entries
     */
    public function getIncome(): Collection;

    /**
     * Get expense entries
     */
    public function getExpenses(): Collection;

    /**
     * Get total amount by type
     */
    public function getTotalByType(AccountingCategoryType $type, ?string $from, ?string $to): float;

    /**
     * Get total income
     */
    public function getTotalIncome(?string $from, ?string $to): float;

    /**
     * Get total expenses
     */
    public function getTotalExpenses(?string $from, ?string $to): float;

    /**
     * Get net total (income - expenses)
     */
    public function getNetTotal(?string $from, ?string $to): float;

    /**
     * Get entries with filters
     */
    public function getWithFilters(array $filters = []): Collection;

    /**
     * Search entries
     */
    public function search(string $search): Collection;

    /**
     * Get monthly totals for chart data
     */
    public function getMonthlyTotals(?int $year): array;

    /**
     * Get daily totals for a specific period
     */
    public function getDailyTotals(string $from, string $to): array;

    /**
     * New methods for reporting
     */
    public function getIncomeCountByDateRange(?string $fromDate, ?string $toDate): int;
    public function getExpenseCountByDateRange(?string $fromDate, ?string $toDate): int;
    public function getEntriesWithFilters(array $filters): array;
    public function getCategoryBreakdown(?string $fromDate, ?string $toDate): array;
    public function getEntriesForExport(?string $fromDate, ?string $toDate): array;
}
