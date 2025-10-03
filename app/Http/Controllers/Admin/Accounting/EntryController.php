<?php

namespace App\Http\Controllers\Admin\Accounting;

use App\DataTables\Accounting\EntryDataTable;
use App\Enums\AccountingCategoryType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Accounting\EntryRequest;
use App\Models\Accounting\Entry;
use App\Services\Accounting\AccountingEntryService;
use App\Services\Accounting\AccountingCategoryService;
use App\Services\PaymentDetailService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Yoeunes\Toastr\Facades\Toastr;


class EntryController extends Controller
{

    public function __construct(
        protected AccountingEntryService $entryService, 
        protected AccountingCategoryService $accountingCategoryService,
        protected PaymentDetailService $paymentDetailService
        )
    {
        // accounting_entry.list accounting_entry.create accounting_entry.edit accounting_entry.delete
        $this->middleware('permission:accounting_entry.list')->only('index', 'data');
        $this->middleware('permission:accounting_entry.create')->only('create', 'store');
        $this->middleware('permission:accounting_entry.edit')->only('edit', 'update');
        $this->middleware('permission:accounting_entry.delete')->only('destroy');
    }

    public function index(Request $request)
    {
        // Get filter parameters
        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');
        $categoryId = $request->get('category_id');
        $type = $request->get('type');

        // Get filtered statistics
        $totalIncomeEntries = $this->entryService->getTotalIncome($fromDate, $toDate);
        $totalPayments = $this->paymentDetailService->getTotalPayments($fromDate, $toDate);
        $totalExpenses = $this->entryService->getTotalExpenses($fromDate, $toDate);
        $netProfit = $this->entryService->getNetTotal($fromDate, $toDate);

        // If filtering by category or type, we need to adjust the income entries calculation
        if ($categoryId || $type) {
            $filteredQuery = Entry::query();
            
            if ($categoryId) {
                $filteredQuery->where('category_id', $categoryId);
            }
            
            if ($type) {
                $filteredQuery->whereHas('category', function($q) use ($type) {
                    $q->where('type', $type);
                });
            }
            
            if ($fromDate) {
                $filteredQuery->whereDate('transaction_date', '>=', $fromDate);
            }
            
            if ($toDate) {
                $filteredQuery->whereDate('transaction_date', '<=', $toDate);
            }
            
            // Recalculate based on filtered entries
            $filteredEntries = $filteredQuery->with('category')->get();
            $totalIncomeEntries = $filteredEntries->where('category.type', AccountingCategoryType::INCOME->value)->sum('amount');
            $totalExpenses = $filteredEntries->where('category.type', AccountingCategoryType::EXPENSE->value)->sum('amount');
            $netProfit = $totalIncomeEntries - $totalExpenses;
        }

        $statistics = [
            'total_income_entries' => $totalIncomeEntries,
            'total_payments' => $totalPayments,
            'total_income' => $totalIncomeEntries + $totalPayments,
            'total_expenses' => $totalExpenses,
            'net_profit' => $netProfit + $totalPayments, // Include payments in net profit calculation
        ];

        // Get categories and types for filters
        $categories = $this->accountingCategoryService->getAllCategories(['id', 'name']);
        $types = AccountingCategoryType::cases();

        return view('admin.accounting.entry.index', compact('statistics', 'categories', 'types'));
    }

    public function data(EntryDataTable $dataTable, Request $request)
    {
        if ($request->ajax()) {
            return $dataTable->dataTable($dataTable->query(new Entry))->make(true);
        }
        abort(403, 'Unauthorized access.');
    }

    public function create(): View
    {
        $categories = $this->accountingCategoryService->getAllCategories(['id', 'name', 'type']);
        $categories->map(fn($category) => $category->title = '(' . $category->type?->value . ') ');
        return view('admin.accounting.entry.create', compact('categories'));
    }

    public function store(EntryRequest $request): RedirectResponse
    {
        $this->entryService->createEntry($request->validated());
        Toastr::success(__('messages.created_successfully'));
        return redirect()->route('admin.accounting.entries.index');
    }

    public function edit(Entry $entry): View
    {
        $categories = $this->accountingCategoryService->getAllCategories(['id', 'name', 'type']);
        $categories->map(fn($category) => $category->title = '(' . $category->type?->value . ') ');

        return view('admin.accounting.entry.edit', compact('entry', 'categories'));
    }

    public function update(EntryRequest $request, Entry $entry): RedirectResponse
    {
        try {
            $this->entryService->updateEntry($entry->id, $request->validated());
            Toastr::success(__('messages.updated_successfully'));
            return redirect()->route('admin.accounting.entries.index');
        } catch (\Exception $e) {
            Toastr::error(__('messages.error_occurred'));
            return back()->withInput();
        }
    }

    public function destroy(Entry $entry): RedirectResponse
    {
        try {
            $this->entryService->deleteEntry($entry->id);
            Toastr::success(__('messages.deleted_successfully'));
            return redirect()->route('admin.accounting.entries.index');
        } catch (\Exception $e) {
            Toastr::error(__('messages.error_occurred'));
            return back();
        }
    }

    public function statistics(Request $request)
    {
        if ($request->ajax()) {
            // Get filter parameters
            $fromDate = $request->get('from_date');
            $toDate = $request->get('to_date');
            $categoryId = $request->get('category_id');
            $type = $request->get('type');

            // Get filtered statistics
            $totalIncomeEntries = $this->entryService->getTotalIncome($fromDate, $toDate);
            $totalPayments = $this->paymentDetailService->getTotalPayments($fromDate, $toDate);
            $totalExpenses = $this->entryService->getTotalExpenses($fromDate, $toDate);
            $netProfit = $this->entryService->getNetTotal($fromDate, $toDate);
            
            // If filtering by category or type, we need to adjust the calculation
            if ($categoryId || $type) {
                $filteredQuery = Entry::query();
                
                if ($categoryId) {
                    $filteredQuery->where('category_id', $categoryId);
                }
                
                if ($type) {
                    $filteredQuery->whereHas('category', function($q) use ($type) {
                        $q->where('type', $type);
                    });
                }
                
                if ($fromDate) {
                    $filteredQuery->whereDate('transaction_date', '>=', $fromDate);
                }
                
                if ($toDate) {
                    $filteredQuery->whereDate('transaction_date', '<=', $toDate);
                }
                
                // Recalculate based on filtered entries
                $filteredEntries = $filteredQuery->with('category')->get();
                $totalIncomeEntries = $filteredEntries->where('category.type', AccountingCategoryType::INCOME->value)->sum('amount');
                $totalExpenses = $filteredEntries->where('category.type', AccountingCategoryType::EXPENSE->value)->sum('amount');
                $netProfit = $totalIncomeEntries - $totalExpenses;
            }

            $statistics = [
                'total_income_entries' => number_format($totalIncomeEntries, 2),
                'total_payments' => number_format($totalPayments, 2),
                'total_income' => number_format($totalIncomeEntries + $totalPayments, 2),
                'total_expenses' => number_format($totalExpenses, 2),
                'net_profit' => number_format($netProfit + $totalPayments, 2),
            ];

            return response()->json($statistics);
        }
        
        abort(403, 'Unauthorized access.');
    }
}
