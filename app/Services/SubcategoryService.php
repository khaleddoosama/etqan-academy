<?php
// app/Services/CategoryService.php

namespace App\Services;


use App\Models\Subcategory;
use Illuminate\Database\Eloquent\Collection;
use Yoeunes\Toastr\Facades\Toastr;

class SubcategoryService
{
    public function getAllSubcategories(): Collection
    {
        return Subcategory::all();
    }

    public function createSubcategory(array $data): Subcategory
    {
        $data['slug'] = str_replace(' ', '-', $data['name']);
        return Subcategory::create($data);
    }

    public function updateSubcategory(Subcategory $subcategory, array $data): bool
    {
        $data['slug'] = str_replace(' ', '-', $data['name']);
        $subcategory->update($data);

        return $subcategory->wasChanged();
    }

    public function deleteSubcategory(Subcategory $subcategory): bool
    {
        return $subcategory->delete();
    }
}
// app/Http/Controllers/Admin/CategoryController.php
