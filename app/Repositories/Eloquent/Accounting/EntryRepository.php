<?php

namespace App\Repositories\Eloquent\Accounting;

use App\Models\Accounting\Entry;
use App\Repositories\Contracts\Accounting\EntryRepositoryInterface;
use App\Repositories\Eloquent\BaseRepository;
use App\Enums\AccountingCategoryType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * @extends BaseRepository<Entry>
 */
class EntryRepository extends BaseRepository implements EntryRepositoryInterface
{
    /**
     * @return Entry
     */
    protected function model(): Model
    {
        return new Entry();
    }

    /**
     * Get entries with filters for DataTable
     */
    public function getForDataTable(Request $request): LengthAwarePaginator
    {
        $query = $this->model->with(['category']);

        // Apply filters
        if ($request->filled('type')) {
            $query->type($request->type);
        }

        if ($request->filled('category_id')) {
            $query->category($request->category_id);
        }


        if ($request->filled('from') && $request->filled('to')) {
            $query->dateRange($request->from, $request->to);
        }

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Sorting
        $sortColumn = $request->get('sort', 'transaction_date');
        $sortDirection = $request->get('direction', 'desc');

        $query->orderBy($sortColumn, $sortDirection);

        return $query->paginate($request->get('per_page', 15));
    }

    /**
     * Get entries by type
     */
    public function getByType(AccountingCategoryType $type): Collection
    {
        return $this->model
            ->type($type)
            ->with(['category'])
            ->orderBy('transaction_date', 'desc')
            ->get();
    }

    /**
     * Get entries by category
     */
    public function getByCategory(int $categoryId): Collection
    {
        return $this->model
            ->category($categoryId)
            ->with(['category'])
            ->orderBy('transaction_date', 'desc')
            ->get();
    }
    
    /**
     * Get entries by date range
     */
    public function getByDateRange(?string $from, ?string $to): Collection
    {
        return $this->model
            ->dateRange($from, $to)
            ->with(['category'])
            ->orderBy('transaction_date', 'desc')
            ->get();
    }

    /**
     * Get income entries
     */
    public function getIncome(): Collection
    {
        return $this->model
            ->income()
            ->with(['category'])
            ->orderBy('transaction_date', 'desc')
            ->get();
    }

    /**
     * Get expense entries
     */
    public function getExpenses(): Collection
    {
        return $this->model
            ->expense()
            ->with(['category'])
            ->orderBy('transaction_date', 'desc')
            ->get();
    }

    /**
     * Get total amount by type
     */
    public function getTotalByType(AccountingCategoryType $type, ?string $from, ?string $to): float
    {
        $query = $this->model->type($type);

        if ($from || $to) {
            $query->dateRange($from, $to);
        }
        
        return (float) $query->sum('amount');
    }

    /**
     * Get total income
     */
    public function getTotalIncome(?string $from, ?string $to): float
    {
        return $this->getTotalByType(AccountingCategoryType::INCOME, $from, $to);
    }

    /**
     * Get total expenses
     */
    public function getTotalExpenses(?string $from, ?string $to): float
    {
        return $this->getTotalByType(AccountingCategoryType::EXPENSE, $from, $to);
    }

    /**
     * Get net total (income - expenses)
     */
    public function getNetTotal(?string $from, ?string $to): float
    {
        return $this->getTotalIncome($from, $to) - $this->getTotalExpenses($from, $to);
    }

    /**
     * Get entries with filters
     */
    public function getWithFilters(array $filters = []): Collection
    {
        $query = $this->model->with(['category']);

        if (!empty($filters['type'])) {
            $query->type($filters['type']);
        }

        if (!empty($filters['category_id'])) {
            $query->category($filters['category_id']);
        }

        if (!empty($filters['from']) && !empty($filters['to'])) {
            $query->dateRange($filters['from'], $filters['to']);
        }

        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        return $query->orderBy('transaction_date', 'desc')->get();
    }

    /**
     * Search entries
     */
    public function search(string $search): Collection
    {
        return $this->model
            ->search($search)
            ->with(['category'])
            ->orderBy('transaction_date', 'desc')
            ->get();
    }

    /**
     * Get monthly totals for chart data
     */
    public function getMonthlyTotals(?int $year): array
    {
        $year = $year ?: now()->year;

        $income = $this->model
            ->income()
            ->whereYear('transaction_date', $year)
            ->select(
                DB::raw('MONTH(transaction_date) as month'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy(DB::raw('MONTH(transaction_date)'))
            ->pluck('total', 'month')
            ->toArray();

        $expenses = $this->model
            ->expense()
            ->whereYear('transaction_date', $year)
            ->select(
                DB::raw('MONTH(transaction_date) as month'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy(DB::raw('MONTH(transaction_date)'))
            ->pluck('total', 'month')
            ->toArray();

        // Fill missing months with 0
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[$i] = [
                'month' => $i,
                'month_name' => Carbon::create()->month($i)->format('M'),
                'income' => (float) ($income[$i] ?? 0),
                'expenses' => (float) ($expenses[$i] ?? 0),
                'net' => (float) ($income[$i] ?? 0) - (float) ($expenses[$i] ?? 0),
            ];
        }

        return $months;
    }

    /**
     * Get daily totals for a specific period
     */
    public function getDailyTotals(string $from, string $to): array
    {
        $fromDate = Carbon::parse($from);
        $toDate = Carbon::parse($to);

        $income = $this->model
            ->income()
            ->dateRange($from, $to)
            ->select(
                DB::raw('DATE(transaction_date) as date'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy(DB::raw('DATE(transaction_date)'))
            ->pluck('total', 'date')
            ->toArray();

        $expenses = $this->model
            ->expense()
            ->dateRange($from, $to)
            ->select(
                DB::raw('DATE(transaction_date) as date'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy(DB::raw('DATE(transaction_date)'))
            ->pluck('total', 'date')
            ->toArray();

        // Fill missing dates with 0
        $days = [];
        for ($date = $fromDate->copy(); $date->lte($toDate); $date->addDay()) {
            $dateStr = $date->format('Y-m-d');
            $days[] = [
                'date' => $dateStr,
                'formatted_date' => $date->format('M d'),
                'income' => (float) ($income[$dateStr] ?? 0),
                'expenses' => (float) ($expenses[$dateStr] ?? 0),
                'net' => (float) ($income[$dateStr] ?? 0) - (float) ($expenses[$dateStr] ?? 0),
            ];
        }

        return $days;
    }
}
