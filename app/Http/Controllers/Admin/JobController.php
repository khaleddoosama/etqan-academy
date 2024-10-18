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
        $failedJob = DB::table('failed_jobs')->where('uuid', $id)->first();

        if (!$failedJob) {
            Toastr::error('Failed job not found', 'Error');
            return redirect()->back();
        }

        // run artisan command to retry the failed job
        $exitCode = Artisan::call('queue:retry', [
            'id' => $failedJob->uuid,
        ]);

        if ($exitCode === 0) {
            Toastr::success('Job has been retried successfully', 'Success');
        } else {
            Toastr::error('Job retry failed', 'Error');
        }

        return redirect()->back();
    }

    // delete
    public function delete($id)
    {
        // get failed job by id
        $failedJob = DB::table('failed_jobs')->where('uuid', $id)->first();

        if (!$failedJob) {
            Toastr::error('Failed job not found', 'Error');
            return redirect()->back();
        }

        // run artisan command to delete the failed job
        $exitCode = Artisan::call('queue:forget', [
            'id' => $failedJob->uuid,
        ]);

        if ($exitCode === 0) {
            Toastr::success('Job has been deleted successfully', 'Success');
        } else {
            Toastr::error('Job delete failed', 'Error');
        }

        return redirect()->back();
    }

    // retry all
    public function retryAll()
    {
        // run artisan command to retry all failed jobs
        $exitCode = Artisan::call('queue:retry', [
            'id' => 'all',
        ]);

        if ($exitCode === 0) {
            Toastr::success('All jobs have been retried successfully', 'Success');
        } else {
            Toastr::error('Jobs retry failed', 'Error');
        }

        return redirect()->back();
    }

    // delete all
    public function deleteAll()
    {
        // run artisan command to delete all failed jobs
        $exitCode = Artisan::call('queue:flush');

        if ($exitCode === 0) {
            Toastr::success('All failed jobs have been deleted successfully', 'Success');
        } else {
            Toastr::error('Failed jobs delete failed', 'Error');
        }

        return redirect()->back();
    }
}
