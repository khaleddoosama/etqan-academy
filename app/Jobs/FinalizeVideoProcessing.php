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
use GuzzleHttp\Promise;

class FinalizeVideoProcessing implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $lecture;

    public function __construct($lecture)
    {
        $this->lecture = $lecture;
    }

    public function handle()
    {
        $this->updateLecture();
        Log::info('Lecture updated: ' . $this->lecture->id);

        $notification = new LectureStatusNotification($this->lecture->id, 1, $this->lecture->title);
        AdminNotificationService::notifyAdmins($notification, ['course.list', 'course.show']);
        // print time now in log
        Log::info('Time after lecture update and notification: ' . now());
    }

    private function updateLecture()
    {
        DB::transaction(function () {
            try {
                // Fetch the related ConvertedVideo with the specified columns
                $convertedVideo = ConvertedVideo::where('lecture_id', $this->lecture->id)
                    ->select([
                        'mp4_Format_240',
                        'mp4_Format_360',
                        'mp4_Format_480',
                        'mp4_Format_720',
                        'mp4_Format_1080',
                        'webm_Format_240',
                        'webm_Format_360',
                        'webm_Format_480',
                        'webm_Format_720',
                        'webm_Format_1080',
                    ])
                    ->first();

                // Check if at least one converted video exists in the database and on the server
                if ($convertedVideo && $this->anyFileExists($convertedVideo)) {
                    $this->lecture->update(['processed' => true]);
                } else {
                    $this->lecture->update(['processed' => false]);
                }
            } catch (\Exception $e) {
                Log::error('Error during lecture update: ' . $e->getMessage());
                throw $e;
            }
        });
    }

    private function anyFileExists($convertedVideo)
    {
        // $formats = [
        //     'mp4_Format_240', 'mp4_Format_360', 'mp4_Format_480', 'mp4_Format_720', 'mp4_Format_1080',
        //     'webm_Format_240', 'webm_Format_360', 'webm_Format_480', 'webm_Format_720', 'webm_Format_1080'
        // ];

        $formats = [
            'mp4_Format_720', 'mp4_Format_1080',
        ];

        foreach ($formats as $format) {
            $filePath = $convertedVideo->$format;
            if ($filePath && Storage::exists($filePath)) {
                return true;
            }
        }

        return false;
    }

    public function failed($exception)
    {
        Log::error('error: ' . $exception->getMessage());
        Log::error('Exception Trace: ' . $exception->getTraceAsString());
        Log::error('getline: ' . $exception->getLine());
        try {
            $this->lecture->update(['processed' => -1]);
        } catch (\Exception $e) {
            Log::error('Failed to update lecture status to -1: ' . $e->getMessage());
        }
        $notification = new LectureStatusNotification($this->lecture->id, 0, $this->lecture->title);
        AdminNotificationService::notifyAdmins($notification, ['course.list', 'course.show']);
    }
}
