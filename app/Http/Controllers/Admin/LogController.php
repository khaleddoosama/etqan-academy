<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use Yoeunes\Toastr\Facades\Toastr;

class LogController extends Controller
{
    public function index()
    {
        return view('admin.logs.index');
    }

    public function show($type, Request $request)
    {
        // Start building the query
        $query = Activity::with(['causer', 'subject']);

        // Apply type filter if not 'all'
        if ($type !== 'all') {
            $query->where('log_name', 'like', '%' . $type . '%');
        }

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', '%' . $search . '%')
                  ->orWhere('log_name', 'like', '%' . $search . '%')
                  ->orWhere('subject_type', 'like', '%' . $search . '%')
                  ->orWhere('properties', 'like', '%' . $search . '%');
            });
        }

        // Apply datetime range filter
        if ($request->filled('date_from')) {
            try {
                $query->where('created_at', '>=', $request->date_from);
            } catch (\Exception $e) {
                // Handle invalid datetime format gracefully
            }
        }

        if ($request->filled('date_to')) {
            try {
                $query->where('created_at', '<=', $request->date_to);
            } catch (\Exception $e) {
                // Handle invalid datetime format gracefully
            }
        }

        // Get per page value from request or default to 25
        $perPage = $request->get('per_page', 10);

        // Validate per_page value to prevent abuse
        $allowedPerPage = [10, 25, 50, 100];
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 10;
        }

        // Get paginated results
        $logs = $query->orderBy('created_at', 'desc')
                     ->paginate($perPage);

        // Preserve query parameters in pagination
        $logs->appends($request->query());

        // Check if this is an AJAX request
        if ($request->ajax()) {
            return view('admin.logs.logs-table', compact('logs', 'type'))->render();
        }

        return view('admin.logs.show', compact('logs', 'type'));
    }

    public function bulkDelete($type)
    {
        Activity::where('log_name', 'like', '%' . $type . '%')->delete();

        Toastr::success('All logs deleted successfully', 'Success');

        return back();
    }

    public function allFiles()
    {
        $files = glob(storage_path('logs/laravel-*.log'));
        $files = array_map('basename', $files);
        return view('admin.logs.files', compact('files'));
    }

    public function showFile($file)
    {
        $logFile = storage_path('logs/' . $file);
        if (!file_exists($logFile)) {
            return view('admin.logs.show_file', ['log' => '', 'file' => $file]);
        } else {
            $log = file_get_contents($logFile);
            return view('admin.logs.show_file', compact('log', 'file'));
        }
    }

    // download
    public function download($file)
    {
        $logFile = storage_path('logs/' . $file);
        if (file_exists($logFile)) {
            return response()->download($logFile);
        } else {
            return back();
        }
    }

    // delete
    public function delete($file)
    {
        $logFile = storage_path('logs/' . $file);
        if (file_exists($logFile)) {
            unlink($logFile);
        }
        return back();
    }

    // allDatabases
    public function allDatabases()
    {
        $databases = storage_path('backups');
        $databases = array_map('basename', glob($databases . '/*.sql'));

        return view('admin.databases.index', compact('databases'));
    }

    public function downloadDatabase($database)
    {
        $database = storage_path('backups/' . $database);
        if (file_exists($database)) {
            return response()->download($database);
        } else {
            return redirect()->route('admin.databases.index');
        }
    }

    public function deleteDatabase($database)
    {
        $database = storage_path('backups/' . $database);
        if (file_exists($database)) {
            unlink($database);
            Toastr::success('Database deleted successfully', 'Success');
        }
        return redirect()->route('admin.databases.index');
    }
}
