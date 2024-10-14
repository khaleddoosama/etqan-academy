<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index()
    {
        $files = glob(storage_path('logs/laravel-*.log'));
        $files = array_map('basename', $files);
        return view('admin.logs.index', compact('files'));
    }

    public function show($file)
    {
        $logFile = storage_path('logs/' . $file);
        if (!file_exists($logFile)) {
            return view('admin.logs.show', ['log' => '', 'file' => $file]);
        } else {
            $log = file_get_contents($logFile);
            return view('admin.logs.show', compact('log', 'file'));
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
}
