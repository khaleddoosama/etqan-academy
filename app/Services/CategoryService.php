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
        return Category::all();
    }

    public function createCategory(array $data): Category
    {
        $category = Category::create($data);

        return $category;
    }

    public function updateCategory(Category $category, array $data): bool
    {
        // $data['slug'] = str_replace(' ', '-', $data['name']);
        $category->update($data);



        return $category->wasChanged();
    }

    public function deleteCategory(Category $category): bool
    {
        $result = $category->delete();


        return $result;
    }
}
// app/Http/Controllers/Admin/CategoryController.php
