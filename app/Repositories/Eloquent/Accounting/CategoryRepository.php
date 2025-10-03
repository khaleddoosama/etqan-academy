<?php

namespace App\Repositories\Eloquent\Accounting;

use App\Models\Accounting\Category;
use App\Repositories\Contracts\Accounting\CategoryRepositoryInterface;
use App\Repositories\Eloquent\BaseRepository;
use App\Enums\AccountingCategoryType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * @extends BaseRepository<Category>
 */
class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface
{

    protected function model(): Model
    {
        return new Category();
    }


    public function getByType(AccountingCategoryType $type): Collection
    {
        return $this->model
            ->type($type)
            ->orderBy('name')
            ->get();
    }

    public function getActive(): Collection
    {
        return $this->model
            ->active()
            ->orderBy('name')
            ->get();
    }

    public function getActiveByType(AccountingCategoryType $type): Collection
    {
        return $this->model
            ->active()
            ->type($type)
            ->orderBy('name')
            ->get();
    }

    public function search(string $search): Collection
    {
        return $this->model
            ->search($search)
            ->orderBy('name')
            ->get();
    }

    public function toggleStatus(int $id): bool
    {
        $category = $this->find($id);
        if (!$category) {
            return false;
        }

        $category->is_active = !$category->is_active;
        return $category->save();
    }

    public function getOptions(AccountingCategoryType $type = null): array
    {
        $query = $this->model->active();

        if ($type) {
            $query->type($type);
        }

        return $query
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }
}
