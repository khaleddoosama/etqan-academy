<?php
// app/Services/CategoryService.php

namespace App\Services;


use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Yoeunes\Toastr\Facades\Toastr;

class CategoryService
{
    public function getCategories(): Collection
    {
        // Adding cache for retrieving categories
        return Cache::remember('categories', 60, function () {
            return Category::all();
        });
    }

    public function createCategory(array $data): Category
    {
        $category = Category::create($data);

        // Clear cache after creating a new category
        Cache::forget('categories');

        return $category;
    }

    public function updateCategory(Category $category, array $data): bool
    {
        // $data['slug'] = str_replace(' ', '-', $data['name']);
        $category->update($data);

        // Clear cache after updating a category
        Cache::forget('categories');

        return $category->wasChanged();
    }

    public function deleteCategory(Category $category): bool
    {
        $result = $category->delete();

        // Clear cache after deleting a category
        Cache::forget('categories');

        return $result;
    }
}
// app/Http/Controllers/Admin/CategoryController.php
