<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryRequest;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Yoeunes\Toastr\Facades\Toastr;


class CategoryController extends Controller
{

    protected CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;

        // category.list category.create category.edit category.delete
        $this->middleware('permission:category.list')->only('index');
        $this->middleware('permission:category.create')->only('create', 'store');
        $this->middleware('permission:category.edit')->only('edit', 'update');
        $this->middleware('permission:category.delete')->only('destroy');
    }

    public function index(): View
    {
        $categories = $this->categoryService->getCategories();
        return view('admin.category.index', compact('categories'));
    }


    public function create(): View
    {
        return view('admin.category.create');
    }


    public function store(CategoryRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $this->categoryService->createCategory($data);

        Toastr::success(__('messages.category_created'), __('status.success'));
        return redirect()->route('admin.categories.index');
    }





    public function edit(Category $category): View
    {
        return view('admin.category.edit', compact('category'));
    }



    public function update(Category $category, CategoryRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $this->categoryService->updateCategory($category, $data) ?
            Toastr::success(__('messages.category_updated'), __('status.success')) :
            '';

        return redirect()->route('admin.categories.index');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $this->categoryService->deleteCategory($category) ?
            Toastr::success(__('messages.category_deleted'), __('status.success')) :
            '';


        return redirect()->route('admin.categories.index');
    }
}
