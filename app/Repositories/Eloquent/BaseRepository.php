<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function filterByRequest(Request $request): array
    {
        $filters = $request->query(); // all URL parameters

        $query = clone $this->query; // Important: don't mutate the original query

        foreach ($filters as $field => $value) {
            if (in_array($field, $this->filterable())) {
                $query->where($field, 'like', "%$value%");
            }
        }

        return $query->get()->toArray();
    }

    /**
     * @return string[]
     */
    protected function filterable(): array
    {
        return [];
    }

    public function createWithItems(array $mainData, string $relationMethod, array $relatedDataArray): Model
    {
        return DB::transaction(function () use ($mainData, $relationMethod, $relatedDataArray) {
            $model = $this->model->create($mainData);

            // Make sure the relation method exists and is callable
            if (!method_exists($model, $relationMethod)) {
                throw new \InvalidArgumentException("Relation method [$relationMethod] does not exist.");
            }

            $model->{$relationMethod}()->createMany($relatedDataArray);

            return $model->load($relationMethod);
        });
    }

    public function updateWithItems(Model $model, array $mainData, string $relationMethod, array $relatedDataArray): Model
    {
        // dd($model, $mainData, $relationMethod, $relatedDataArray);
        return DB::transaction(function () use ($model, $mainData, $relationMethod, $relatedDataArray) {
            // Update main model
            $model->update($mainData);

            if (!method_exists($model, $relationMethod)) {
                throw new \InvalidArgumentException("Relation method [$relationMethod] does not exist.");
            }

            $relation = $model->{$relationMethod};
            $existingIds = $relation->pluck('id')->toArray();

            $incomingIds = collect($relatedDataArray)->pluck('id')->filter()->toArray();

            $idsToDelete = array_diff($existingIds, $incomingIds);
            if (!empty($idsToDelete)) {
                // $relation->whereIn('id', $idsToDelete)->get()->each(function ($item) {
                //     $item->delete();
                // });
                foreach ($idsToDelete as $id) {
                    $relation->find($id)->delete();
                }
            }

            foreach ($relatedDataArray as $itemData) {
                if (isset($itemData['id']) && in_array($itemData['id'], $existingIds)) {
                    // $relation->where('id', $itemData['id'])->update($itemData);
                    $relation->find($itemData['id'])->update($itemData);
                } else {
                    // $relation->create($itemData);
                    $model->{$relationMethod}()->create($itemData);
                }
            }

            return $model->load($relationMethod);
        });
    }
}
