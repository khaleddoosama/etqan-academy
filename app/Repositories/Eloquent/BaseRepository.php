<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * @template TModel of Model
 * @implements BaseRepositoryInterface<TModel>
 */
abstract class BaseRepository implements BaseRepositoryInterface
{
    /** @var TModel */
    protected Model $model;

    /** @var Builder */
    protected Builder $query;

    public function __construct()
    {
        $this->model = $this->model();
        $this->query = $this->model->newQuery();
    }

    /**
     * @return TModel
     */
    abstract protected function model(): Model;

    public function all(array $columns = ['*'], array $with = []): Collection
    {
        return $this->model->with($with)->select($columns)->latest()->get();
    }

    public function find($id, array $columns = ['*'], array $with = []): ?Model
    {
        return $this->model->with($with)->select($columns)->findOrFail($id);
    }

    public function findBy(string $column, $value, array $columns = ['*'], array $with = []): ?Model
    {
        return $this->model->with($with)->select($columns)->where($column, $value)->first();
    }

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function update($id, array $data): bool
    {
        $record = $this->find($id);
        if (!$record) {
            return false;
        }
        return $record->update($data);
    }

    public function delete($id): bool
    {
        $record = $this->find($id);
        if (!$record) {
            return false;
        }
        return (bool) $record->delete();
    }

    public function where(array $conditions, array $columns = ['*'], array $with = []): Collection
    {
        return $this->model->with($with)->where($conditions)->select($columns)->get();
    }

    public function whereIn(string $column, array $values, array $columns = ['*'], array $with = []): Collection
    {
        return $this->model->with($with)->whereIn($column, $values)->select($columns)->get();
    }

    public function paginate(int $perPage = 15, array $columns = ['*'], array $with = []): LengthAwarePaginator
    {
        return $this->model->with($with)->select($columns)->latest()->paginate($perPage);
    }

    public function orderBy(string $column, string $direction = 'asc'): Model
    {
        return $this->model->orderBy($column, $direction);
    }

    public function attachRelation($id, string $relation, array $relatedIds): bool
    {
        $record = $this->find($id);
        return $record->$relation()->attach($relatedIds);
    }

    public function detachRelation($id, string $relation, array $relatedIds): bool
    {
        $record = $this->find($id);
        return $record->$relation()->detach($relatedIds);
    }

    public function syncRelation($id, string $relation, array $relatedIds): bool
    {
        $record = $this->find($id);
        return $record->$relation()->sync($relatedIds);
    }

    public function first(array $columns = ['*'], array $with = []): ?Model
    {
        return $this->query->with($with)->select($columns)->first();
    }

    public function pluck(string $column, $key = null)
    {
        return $this->query->pluck($column, $key);
    }

    public function count(): int
    {
        return $this->query->count();
    }

    public function filterByRequest(Request $request, array $columns = ['*'], array $with = []): Collection
    {
        $filters = $request->query();

        $query = clone $this->query;

        $filterable = $this->filterable();
        foreach ($filters as $field => $value) {

            if (!array_key_exists($field, $filterable) || !$value) {
                continue;
            }

            $type = $filterable[$field];

            // Date/time range filters with from_ / to_
            Log::info($field);
            Log::info(preg_match('/^(from|to)_(.+)$/', $field, $matches));
            if (preg_match('/^(from|to)_(.+)$/', $field, $matches)) {
                [$_, $dir, $column] = $matches;
                Log::info($column);
                if (!array_key_exists($column, $filterable) || $filterable[$column] !== 'date') {
                    continue;
                }

                $operator = $dir === 'from' ? '>=' : '<=';
                $query->whereDate($column, $operator, $value);
                continue;
            }

            // Filtering logic by type
            match ($type) {
                'exact' => $query->where($field, '=', $value),
                'like'  => $query->where($field, 'like', "%$value%"),
                'date'  => $query->whereDate($field, '=', $value),
                default => null,
            };
        }

        return $query->with($with)->select($columns)->get();
    }

    /**
     * @return string[]
     */
    protected function filterable(): array
    {
        return [];
        /*example:
        return [
            'user_id'   => 'exact',
            'name'      => 'like',
            'created_at' => 'date',
        ];*/
    }
}
