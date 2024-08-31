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
        $this->hours = $hours;
        $this->minutes = $minutes;
        $this->seconds = $seconds;
        $this->quality = $quality;
        $this->videoPath = $videoPath;
    }

    public function handle()
    {
        $this->deleteOldVideo();
        $this->updateConvertedVideo();
        $this->updateLecture($this->hours, $this->minutes, $this->seconds, $this->quality);
        Log::info('Lecture updated: ' . $this->lecture->id);

        $notification = new LectureStatusNotification($this->lecture->id, 1);
        AdminNotificationService::notifyAdmins($notification);
    }

    private function deleteOldVideo()
    {
        if (file_exists($this->videoPath)) {
            unlink($this->videoPath);
        }
        if (Storage::disk($this->lecture->disk)->exists($this->lecture->video)) {
            Storage::disk($this->lecture->disk)->delete($this->lecture->video);
        }
    }

    private function updateConvertedVideo()
    {
        $names = [
            'mp4_Format_1080' => str_replace('//', '/', $this->getFileName($this->lecture->video, 'mp4', '1080p')),
            'webm_Format_1080' => str_replace('//', '/', $this->getFileName($this->lecture->video, 'webm', '1080p')),
            'mp4_Format_720' => str_replace('//', '/', $this->getFileName($this->lecture->video, 'mp4', '720p')),
            'webm_Format_720' => str_replace('//', '/', $this->getFileName($this->lecture->video, 'webm', '720p')),
            'mp4_Format_480' => str_replace('//', '/', $this->getFileName($this->lecture->video, 'mp4', '480p')),
            'webm_Format_480' => str_replace('//', '/', $this->getFileName($this->lecture->video, 'webm', '480p')),
            'mp4_Format_360' => str_replace('//', '/', $this->getFileName($this->lecture->video, 'mp4', '360p')),
            'webm_Format_360' => str_replace('//', '/', $this->getFileName($this->lecture->video, 'webm', '360p')),
            'mp4_Format_240' => str_replace('//', '/', $this->getFileName($this->lecture->video, 'mp4', '240p')),
            'webm_Format_240' => str_replace('//', '/', $this->getFileName($this->lecture->video, 'webm', '240p'))
        ];

        ConvertedVideo::updateOrCreate(
            ['lecture_id' => $this->lecture->id],
            $names
        );
    }

    private function updateLecture(int $hours, int $minutes, int $seconds, int $quality)
    {
        $this->lecture->update([
            'processed' => true,
            'hours' => $hours,
            'minutes' => $minutes,
            'seconds' => $seconds,
            'quality' => $quality
        ]);
    }

    private function getFileName($fileName, $type, $quality)
    {
        return preg_replace('/\\.[^.\\s]{3,4}$/', '', $fileName) . '-' . $quality . '.' . $type;
    }

    // faild
    public function failed($exception)
    {
        Log::error('error: ' . $exception->getMessage());
        Log::error('Exception Trace: ' . $exception->getTraceAsString());
        Log::error('getline: ' . $exception->getLine());
        $this->lecture->update(['processed' => -1]);

        $notification = new LectureStatusNotification($this->lecture->id, 0);
        AdminNotificationService::notifyAdmins($notification);
    }
}
