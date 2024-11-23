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

    public function show($type)
    {
        $logs = Activity::with('causer')->where('log_name', 'like', '%' . $type . '%')
            ->orderBy('created_at', 'desc')
            ->paginate(100);

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
