<?php

namespace App\Http\Controllers\Admin\Accounting;

use App\Enums\AccountingCategoryType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Accounting\CategoryRequest;
use App\Models\Accounting\Category;
use App\Services\Accounting\AccountingCategoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Yoeunes\Toastr\Facades\Toastr;


class CategoryController extends Controller
{


    public function __construct( protected AccountingCategoryService $categoryService)
    {
        // accounting_category.list accounting_category.create accounting_category.edit accounting_category.delete
        $this->middleware('permission:accounting_category.list')->only('index');
        $this->middleware('permission:accounting_category.create')->only('create', 'store');
        $this->middleware('permission:accounting_category.edit')->only('edit', 'update');
        $this->middleware('permission:accounting_category.delete')->only('destroy');
    }

    public function index(): View
    {
        $categories = $this->categoryService->getAllCategories();
        return view('admin.accounting.category.index', compact('categories'));
    }


    public function create(): View
    {
        $typeOptions = AccountingCategoryType::options();
        return view('admin.accounting.category.create', compact('typeOptions'));
    }


    public function store(CategoryRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $this->categoryService->createCategory($data);

        Toastr::success(__('messages.category_created'), __('status.success'));

        return redirect()->route('admin.accounting.categories.index');
    }

    public function edit(Category $category): View
    {
            $typeOptions = AccountingCategoryType::options();
        return view('admin.accounting.category.edit', compact('category', 'typeOptions'));
    }

    public function update(int $id, CategoryRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $this->categoryService->updateCategory($id, $data) ?
            Toastr::success(__('messages.category_updated'), __('status.success')) :
            '';

        return redirect()->route('admin.accounting.categories.index');
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->categoryService->deleteCategory($id) ?
            Toastr::success(__('messages.category_deleted'), __('status.success')) :
            '';


        return redirect()->route('admin.accounting.categories.index');
    }
}
