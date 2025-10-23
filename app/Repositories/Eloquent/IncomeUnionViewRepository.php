<?php

namespace App\Repositories\Eloquent;

use App\Models\IncomeUnionView;
use App\Repositories\Contracts\IncomeUnionViewRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class IncomeUnionViewRepository implements IncomeUnionViewRepositoryInterface
{
    public function __construct(private readonly IncomeUnionView $model)
    {
    }

    public function getIncomes(array $filters = []): Collection
    {
        return $this->applyBaseFilters($filters)
            ->orderByDesc('paid_at')
            ->orderByDesc('created_at')
            ->get();
    }

    public function getTotalAmount(array $filters = []): float
    {
        return (float) $this->applyBaseFilters($filters)->sum('amount');
    }

    public function getCount(array $filters = []): int
    {
        return (int) $this->applyBaseFilters($filters)->count();
    }

    public function getMonthlyTotals(array $filters = []): array
    {
        $query = $this->applyBaseFilters($filters);

        return $query
            ->selectRaw("DATE_FORMAT(paid_at, '%Y-%m') AS period, SUM(amount) AS total")
            ->groupBy('period')
            ->orderBy('period')
            ->pluck('total', 'period')
            ->map(fn ($value) => (float) $value)
            ->toArray();
    }

    public function getPaginatedIncomes(
        array $filters,
        int $start,
        int $length,
        string $searchValue = '',
        int $orderColumn = 6,
        string $orderDir = 'desc'
    ): array {
        $baseQuery = $this->applyBaseFilters($filters);

        $totalRecords = (clone $baseQuery)->count();

        $filteredQuery = clone $baseQuery;

        if (!empty($searchValue)) {
            $filteredQuery->search($searchValue);
        }

        $recordsFiltered = !empty($searchValue)
            ? (clone $filteredQuery)->count()
            : $totalRecords;

        $orderColumnName = $this->resolveOrderColumn($orderColumn);
        $direction = strtolower($orderDir) === 'asc' ? 'asc' : 'desc';

        $filteredQuery->orderBy($orderColumnName, $direction)
            ->orderByDesc('id');

        $data = $filteredQuery
            ->skip($start)
            ->take($length)
            ->get();

        return [
            'data' => $data,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $recordsFiltered,
        ];
    }

    protected function applyBaseFilters(array $filters): Builder
    {
        $query = $this->model->newQuery();

        $fromDate = $filters['from_date'] ?? $filters['from'] ?? null;
        $toDate = $filters['to_date'] ?? $filters['to'] ?? null;
        $categoryId = isset($filters['category_id']) ? (int) $filters['category_id'] : null;
        $type = $filters['type'] ?? null;

        if ($fromDate || $toDate) {
            $query->byDateRange($fromDate, $toDate);
        }

        if ($categoryId) {
            $query->byCategoryId($categoryId);
        }

        if ($type) {
            $query->byType($type);
        }

        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        return $query;
    }

    protected function resolveOrderColumn(int $columnIndex): string
    {
        return match ($columnIndex) {
            0 => 'id',
            1 => 'type',
            2 => 'title',
            3 => 'details',
            4 => 'category',
            5 => 'amount',
            default => 'paid_at',
        };
    }
}
