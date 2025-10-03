<?php

namespace App\Http\Controllers\Admin\Accounting;

use App\Http\Controllers\Controller;
use App\Services\Accounting\AccountingReportService;
use App\Services\Accounting\AccountingEntryService;
use App\Services\PaymentDetailService;
use App\Enums\AccountingCategoryType;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function __construct(
        protected AccountingReportService $reportService,
        protected AccountingEntryService $entryService,
        protected PaymentDetailService $paymentService
    ) {
        $this->middleware('permission:accounting_report.view')->only('index', 'data', 'charts');
    }

    public function index(Request $request): View
    {
        // Get filter parameters
        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');
        $categoryId = $request->get('category_id');
        $type = $request->get('type');

        // Get summary statistics through service
        $summary = $this->reportService->getSummaryData($fromDate, $toDate, $categoryId, $type);        // Get categories for filter
        $categories = $this->reportService->getCategories();
        $types = AccountingCategoryType::cases();

        return view('admin.accounting.report.index', compact('summary', 'categories', 'types', 'fromDate', 'toDate'));
    }

    public function data(Request $request): JsonResponse
    {
        if (!$request->ajax()) {
            abort(403, 'Unauthorized access.');
        }

        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');
        $categoryId = $request->get('category_id');
        $type = $request->get('type');

        // Get DataTable parameters
        $start = (int) $request->get('start', 0);
        $length = (int) $request->get('length', 25);
        $searchValue = $request->get('search')['value'] ?? '';
        $orderColumn = $request->get('order')[0]['column'] ?? 6; // Default to date column
        $orderDir = $request->get('order')[0]['dir'] ?? 'desc';

        // Get paginated combined income data through service
        $result = $this->reportService->getPaginatedCombinedIncomeData(
            $fromDate,
            $toDate,
            $categoryId,
            $type,
            $start,
            $length,
            $searchValue,
            $orderColumn,
            $orderDir
        );

        $response = [
            'draw' => (int) $request->get('draw', 1),
            'data' => $result['data'],
            'recordsTotal' => $result['recordsTotal'],
            'recordsFiltered' => $result['recordsFiltered']
        ];

        // Debug logging (remove in production)
        Log::info('DataTable Response', [
            'start' => $start,
            'length' => $length,
            'total' => $result['recordsTotal'],
            'filtered' => $result['recordsFiltered'],
            'data_count' => count($result['data'])
        ]);

        return response()->json($response);
    }

    public function charts(Request $request): JsonResponse
    {
        if (!$request->ajax()) {
            abort(403, 'Unauthorized access.');
        }

        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');

        return response()->json([
            'monthly_trends' => $this->reportService->getMonthlyTrends($fromDate, $toDate),
            'income_vs_expenses' => $this->reportService->getIncomeVsExpenses($fromDate, $toDate),
            'payment_methods' => $this->reportService->getPaymentMethodsBreakdown($fromDate, $toDate)
        ]);
    }

    public function export(Request $request)
    {
        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');
        $format = $request->get('format', 'xlsx');

        // Get export data through service
        $exportData = $this->reportService->getExportData($fromDate, $toDate);

        $filename = 'accounting_report_' . $fromDate . '_to_' . $toDate . '.' . $format;

        // For now, return JSON data. We can implement Excel export later
        if ($format === 'json') {
            return response()->json($exportData);
        }

        // TODO: Implement Excel export using the export data
        return response()->download($this->reportService->generateExcelFile($exportData, $filename));
    }

    public function statistics(Request $request): JsonResponse
    {
        if (!$request->ajax()) {
            abort(403, 'Unauthorized access.');
        }

        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');
        $categoryId = $request->get('category_id');
        $type = $request->get('type');

        $summary = $this->reportService->getSummaryData($fromDate, $toDate, $categoryId, $type);

        return response()->json([
            'total_income' => number_format($summary['total_income'], 2),
            'total_expenses' => number_format($summary['total_expenses'], 2),
            'net_profit' => number_format($summary['net_profit'], 2),
            'profit_margin' => $summary['profit_margin'] . '%'
        ]);
    }

    public function updateTable(Request $request): JsonResponse
    {
        if (!$request->ajax()) {
            abort(403, 'Unauthorized access.');
        }

        // This will be handled by the existing data() method
        return $this->data($request);
    }
}
