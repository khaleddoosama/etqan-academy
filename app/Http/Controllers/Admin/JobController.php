<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Yoeunes\Toastr\Facades\Toastr;

class JobController extends Controller
{
    public function index()
    {
        // return jobs order by reserved_at
        $jobs = DB::table('jobs')->orderBy('reserved_at')->get();

        return view('admin.jobs.index', compact('jobs'));
    }

    // get failed_jobs
    public function failedJobs()
    {
        // return failed jobs order by failed_at
        $failedJobs = DB::table('failed_jobs')->orderBy('failed_at')->get();

        return view('admin.failed_jobs.index', compact('failedJobs'));
    }

    // retry
    public function retry($id)
    {
        // get failed job by id
        $failedJob = DB::table('failed_jobs')->where('id', $id)->first();

        if (!$failedJob) {
            Toastr::error('Failed job not found', 'Error');
            return redirect()->back();
        }

        // run artisan command to retry the failed job
        $exitCode = Artisan::call('queue:retry', [
            'id' => $failedJob->id,
        ]);

        if ($exitCode === 0) {
            Toastr::success('Job has been retried successfully', 'Success');
        } else {
            Toastr::error('Job retry failed', 'Error');
        }

        return redirect()->back();
    }
}
