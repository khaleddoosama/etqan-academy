<?php

namespace App\Services\Accounting;

use App\Models\Accounting\Entry;
use App\Repositories\Contracts\Accounting\EntryRepositoryInterface;
use App\Repositories\Contracts\Accounting\CategoryRepositoryInterface;
use App\Enums\AccountingCategoryType;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class AccountingEntryService
{
    public function __construct(
        private EntryRepositoryInterface $entryRepository,
        private CategoryRepositoryInterface $categoryRepository
    ) {}


    public function getEntriesForDataTable(Request $request): LengthAwarePaginator
    {
        return $this->entryRepository->getForDataTable($request);
    }

    public function getEntries(array $filters = []): Collection
    {
        if (empty($filters)) {
            return $this->entryRepository->all(['*'], ['category']);
        }

        return $this->entryRepository->getWithFilters($filters);
    }

    public function getEntriesByType(AccountingCategoryType $type): Collection
    {
        return $this->entryRepository->getByType($type);
    }

    public function findEntry(int $id): ?Entry
    {
        return $this->entryRepository->find($id, ['*'], ['category']);
    }

    public function createEntry(array $data): Entry
    {
        try {
            DB::beginTransaction();

            // Validate category exists and is active
            $category = $this->categoryRepository->find($data['category_id']);
            if (!$category || !$category->is_active) {
                throw new Exception('Invalid or inactive category');
            }

            $entry = $this->entryRepository->create($data);

            DB::commit();

            return $entry;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateEntry(int $id, array $data): bool
    {
        try {
            DB::beginTransaction();

            $entry = $this->findEntry($id);
            if (!$entry) {
                return false;
            }

            // If category is being changed, validate it
            if (isset($data['category_id']) && $data['category_id'] != $entry->category_id) {
                $category = $this->categoryRepository->find($data['category_id']);
                if (!$category || !$category->is_active) {
                    throw new Exception('Invalid or inactive category');
                }
                $data['type'] = $category->type->value;
            }

            $updated = $this->entryRepository->update($id, $data);

            DB::commit();

            return $updated;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }


    public function deleteEntry(int $id): bool
    {
        try {
            DB::beginTransaction();

            $deleted = $this->entryRepository->delete($id);

            DB::commit();

            return $deleted;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get income entries
     */
    public function getIncomeEntries(): Collection
    {
        return $this->entryRepository->getIncome();
    }

    /**
     * Get expense entries
     */
    public function getExpenseEntries(): Collection
    {
        return $this->entryRepository->getExpenses();
    }

    /**
     * Get total income
     */
    public function getTotalIncome(?string $from, ?string $to): float
    {
        return $this->entryRepository->getTotalIncome($from, $to);
    }

    /**
     * Get total expenses
     */
    public function getTotalExpenses(?string $from, ?string $to): float
    {
        return $this->entryRepository->getTotalExpenses($from, $to);
    }

    /**
     * Get net total (income - expenses)
     */
    public function getNetTotal(?string $from, ?string $to): float
    {
        return $this->entryRepository->getNetTotal($from, $to);
    }

    /**
     * Search entries
     */
    public function searchEntries(string $search): Collection
    {
        return $this->entryRepository->search($search);
    }

    /**
     * Get monthly totals for charts
     */
    public function getMonthlyTotals(int $year = null): array
    {
        return $this->entryRepository->getMonthlyTotals($year);
    }

    /**
     * Get daily totals for charts
     */
    public function getDailyTotals(string $from, string $to): array
    {
        return $this->entryRepository->getDailyTotals($from, $to);
    }
    /**
     * Get summary statistics
     */
    public function getSummaryStats(?string $from, ?string $to): array
    {
        return [
            'total_income' => $this->getTotalIncome($from, $to),
            'total_expenses' => $this->getTotalExpenses($from, $to),
            'net_total' => $this->getNetTotal($from, $to),
            'income_count' => $this->entryRepository->getByType(AccountingCategoryType::INCOME)->count(),
            'expense_count' => $this->entryRepository->getByType(AccountingCategoryType::EXPENSE)->count(),
        ];
    }
}
