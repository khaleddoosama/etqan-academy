<?php

namespace App\Services\Accounting;

use App\Models\Accounting\Category;
use App\Repositories\Contracts\Accounting\CategoryRepositoryInterface;
use App\Enums\AccountingCategoryType;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Exception;

class AccountingCategoryService
{
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository
    ) {}

    public function getAllCategories(array $with = []): Collection
    {
        return $this->categoryRepository->all(['*'], $with);
    }

    public function getCategoriesByType(AccountingCategoryType $type): Collection
    {
        return $this->categoryRepository->getByType($type);
    }

    public function getActiveCategories(): Collection
    {
        return $this->categoryRepository->getActive();
    }

    public function getActiveCategoriesByType(AccountingCategoryType $type): Collection
    {
        return $this->categoryRepository->getActiveByType($type);
    }

    public function findCategory(int $id): ?Category
    {
        return $this->categoryRepository->find($id);
    }

    public function createCategory(array $data): Category
    {
        try {
            DB::beginTransaction();

            $category = $this->categoryRepository->create($data);

            DB::commit();

            return $category;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateCategory(int $id, array $data): bool
    {
        try {
            DB::beginTransaction();

            $updated = $this->categoryRepository->update($id, $data);

            DB::commit();

            return $updated;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function deleteCategory(int $id): bool
    {
        try {
            DB::beginTransaction();

            $category = $this->findCategory($id);
            if (!$category) {
                return false;
            }

            // Check if category has entries
            if ($category->entries()->count() > 0) {
                throw new Exception('Cannot delete category with existing entries');
            }

            $deleted = $this->categoryRepository->delete($id);

            DB::commit();

            return $deleted;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function toggleStatus(int $id): bool
    {
        try {
            DB::beginTransaction();

            $toggled = $this->categoryRepository->toggleStatus($id);

            DB::commit();

            return $toggled;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function searchCategories(string $search): Collection
    {
        return $this->categoryRepository->search($search);
    }

    public function getCategoryOptions(AccountingCategoryType $type = null): array
    {
        return $this->categoryRepository->getOptions($type);
    }

    public function getIncomeCategoryOptions(): array
    {
        return $this->getCategoryOptions(AccountingCategoryType::INCOME);
    }

    public function getExpenseCategoryOptions(): array
    {
        return $this->getCategoryOptions(AccountingCategoryType::EXPENSE);
    }

    public function isCategoryNameUnique(string $name, AccountingCategoryType $type, int $excludeId = null): bool
    {
        $existing = $this->categoryRepository->where([
            'name' => $name,
            'type' => $type->value,
        ]);

        if ($excludeId) {
            $existing = $existing->where('id', '!=', $excludeId);
        }

        return $existing->isEmpty();
    }
}
