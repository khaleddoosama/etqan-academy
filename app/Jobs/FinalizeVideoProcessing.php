<?php

namespace App\Jobs;

use App\Models\ConvertedVideo;
use App\Notifications\LectureStatusNotification;
use App\Services\AdminNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FinalizeVideoProcessing implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $lecture;
    protected $hours;
    protected $minutes;
    protected $seconds;
    protected $quality;
    protected $videoPath;

    public function __construct($lecture, $hours, $minutes, $seconds, $quality, $videoPath)
    {
        $this->lecture = $lecture;
    }

    public function handle()
    {
        $this->updateLecture();
        Log::info('Lecture updated: ' . $this->lecture->id);

        $notification = new LectureStatusNotification($this->lecture->id, 1);
        AdminNotificationService::notifyAdmins($notification, ['course.list', 'course.show']);
        // print time now in log
        Log::info(message: 'Time after: ' . now());
    }

    private function updateLecture()
    {
        DB::transaction(function () {
            $this->lecture->update([
                'processed' => true,
            ]);
        });
    }

    // faild
    public function failed($exception)
    {
        Log::error('error: ' . $exception->getMessage());
        Log::error('Exception Trace: ' . $exception->getTraceAsString());
        Log::error('getline: ' . $exception->getLine());
        DB::transaction(function () {
            $this->lecture->update(['processed' => -1]);
        });
        $notification = new LectureStatusNotification($this->lecture->id, 0);
        AdminNotificationService::notifyAdmins($notification, ['course.list', 'course.show']);
    }
}
