<?php

namespace App\Repositories\Contracts;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

/**
 * @template TModel of Model
 */
interface BaseRepositoryInterface
{
    /**
     * @return Collection<int, TModel>
     */
    public function all(array $columns = ['*'], array $with = []): Collection;

    /**
     * @return TModel|null
     */
    public function find($id, array $columns = ['*'], array $with = []): ?Model;

    /**
     * @return TModel|null
     */
    public function findBy(string $column, $value, array $columns = ['*'], array $with = []): ?Model;

    /**
     * @param array<string, mixed> $data
     * @return TModel
     */
    public function create(array $data): Model;

    /**
     * @param array<string, mixed> $data
     */
    public function update($id, array $data): bool;

    public function delete($id): bool;

    /**
     * @return Collection<int, TModel>
     */
    public function where(array $conditions, array $columns = ['*'], array $with = []): Collection;

    /**
     * @return Collection<int, TModel>
     */
    public function whereIn(string $column, array $values, array $columns = ['*'], array $with = []): Collection;

    /**
     * @return LengthAwarePaginator<TModel>
     */
    public function paginate(int $perPage = 15, array $columns = ['*'], array $with = []): LengthAwarePaginator;

    /**
     * @return Collection<int, TModel>
     */
    public function orderBy(string $column, string $direction = 'asc'): ?Model;

    /**
     * @param array<int> $relatedIds
     */
    public function attachRelation($id, string $relation, array $relatedIds): bool;

    /**
     * @param array<int> $relatedIds
     */
    public function detachRelation($id, string $relation, array $relatedIds): bool;

    /**
     * @param array<int> $relatedIds
     */
    public function syncRelation($id, string $relation, array $relatedIds): bool;
    /**
     * @return TModel|null
     */
    public function first(array $columns = ['*'], array $with = []): ?Model;

    /**
     * @return Collection|array
     */
    public function pluck(string $column, $key = null);

    public function count(): int;

    /**
     * @return array<string, mixed>
     */
    public function filterByRequest(Request $request): array;

    public function createWithItems(array $mainData, string $relationMethod, array $relatedDataArray): Model;

    public function updateWithItems(Model $model, array $mainData, string $relationMethod, array $relatedDataArray): Model;
}
