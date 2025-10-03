<?php

namespace App\Repositories\Contracts\Accounting;

use App\Models\Accounting\Category;
use App\Repositories\Contracts\BaseRepositoryInterface;
use App\Enums\AccountingCategoryType;
use Illuminate\Support\Collection;

/**
 * @extends BaseRepositoryInterface<Category>
 */
interface CategoryRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get categories by type
     */
    public function getByType(AccountingCategoryType $type): Collection;

    /**
     * Get active categories
     */
    public function getActive(): Collection;

    /**
     * Get active categories by type
     */
    public function getActiveByType(AccountingCategoryType $type): Collection;

    /**
     * Search categories
     */
    public function search(string $search): Collection;

    /**
     * Toggle category status
     */
    public function toggleStatus(int $id): bool;

    /**
     * Get category options for select (id => name)
     */
    public function getOptions(AccountingCategoryType $type = null): array;
}
