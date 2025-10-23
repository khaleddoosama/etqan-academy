<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface IncomeUnionViewRepositoryInterface
{
    public function getIncomes(array $filters = []): Collection;

    public function getTotalAmount(array $filters = []): float;

    public function getCount(array $filters = []): int;

    public function getMonthlyTotals(array $filters = []): array;

    public function getPaginatedIncomes(
        array $filters,
        int $start,
        int $length,
        string $searchValue = '',
        int $orderColumn = 6,
        string $orderDir = 'desc'
    ): array;
}
