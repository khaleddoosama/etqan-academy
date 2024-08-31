<?php

namespace App\Jobs;

use App\Models\ConvertedVideo;
use App\Notifications\LectureStatusNotification;
use App\Services\AdminNotificationService;
use App\Services\AwsS3Service;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Str;

class DuplicateLecture implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $lecture;
    public $new_lecture;

    public function __construct($lecture, $new_lecture)
    {
        $this->lecture = $lecture;
        $this->new_lecture = $new_lecture;
    }


    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $awsS3Service = app(AwsS3Service::class);

        $formats = [
            'mp4' => ['1080', '720', '480', '360', '240'],
            'webm' => ['1080', '720', '480', '360', '240']
        ];

        // get converted video
        $convertedVideo = $this->lecture->convertedVideo;

        // copy the video to the new path and rename it to the new name in Storage
        // $newPathFolder = $this->getSanitizedPath();
        $newPathFolder = 'uploads/' . $this->new_lecture->section->course->slug . '/' . $this->new_lecture->section->slug . '/' . $this->new_lecture->slug;
        $newName = hexdec(uniqid());
        Log::info('newPathFolder: ' . $newPathFolder);
        // Log::info('convertedVideo: ' . $convertedVideo);
        if ($convertedVideo) {

            $this->new_lecture->update([
                'video' => "{$newPathFolder}/videos/{$newName}.mp4",
            ]);

            $pathes = [];
            foreach ($formats as $format => $resolutions) {
                foreach ($resolutions as $resolution) {
                    $method = "{$format}_Format_{$resolution}";
                    $path = Storage::path($convertedVideo->$method);

                    if ($path && Storage::exists($path)) {

                        $newFilePath =  "{$newPathFolder}/videos/{$newName}-{$resolution}.{$format}";

                        $awsS3Service->duplicateObject($path, $newFilePath);

                        $pathes[$method] = $newFilePath;
                    }
                }
            }

            $this->updateModel($pathes, $newPathFolder . '/videos/' . $newName . '.mp4');
        }

        // get thumbnail
        $thumbnail = $this->lecture->thumbnail;

        // // copy the thumbnail to the new path and rename it to the new name in Storage
        if ($thumbnail) {
            $path = Storage::path($thumbnail);
            if ($path && Storage::exists($path)) {
                // $ext = split('.', $path)[1];
                $ext = pathinfo($path, PATHINFO_EXTENSION);
                $newFilePath = $newPathFolder . '/thumbnails/' . $newName . '.' . $ext;
                // Storage::put($newFilePath, Storage::get($path));
                $awsS3Service->duplicateObject($path, $newFilePath);

                DB::table('lectures')->where('id', $this->new_lecture->id)->update(['thumbnail' => $newFilePath]);
            }
        }




        // get attachments
        $attachments = $this->lecture->attachments;

        // copy the attachments to the new path and rename it to the new name in Storage
        if ($attachments) {
            Log::info('attachments: ' . json_encode($attachments));
            //$attachments = [{"path":"uploads\/tkn-mal\/s1-mhtw\/fifth\/attachments\/1806316249692586.mp4","originalName":"(480).mp4","type":"video\/mp4"},{"path":"uploads\/tkn-mal\/s1-mhtw\/fifth\/attachments\/1806316254692107.jpg","originalName":"170732977_3908137575960956_4157816659971560064_n.jpg","type":"image\/jpeg"},{"path":"uploads\/tkn-mal\/s1-mhtw\/fifth\/attachments\/1806316255318982.pdf","originalName":"DevKhaled.pdf","type":"application\/pdf"},{"path":"uploads\/tkn-mal\/s1-mhtw\/fifth\/attachments\/1806316255999655.mp3","originalName":"file_example_MP3_1MG.mp3","type":"audio\/mpeg"}]
            foreach ($attachments as &$attachment) {
                $path = Storage::path($attachment['path']);
                if ($path && Storage::exists($path)) {
                    $ext = pathinfo($path, PATHINFO_EXTENSION);
                    $newFilePath = $newPathFolder . '/attachments/' . $newName . '.' . $ext;

                    $awsS3Service->duplicateObject($path, $newFilePath);
                    $attachment['path'] = $newFilePath;

                }
            }
            DB::table('lectures')->where('id', $this->new_lecture->id)->update(['attachments' => json_encode($attachments)]);
        }

        $notification = new LectureStatusNotification($this->new_lecture->id, 1);
        AdminNotificationService::notifyAdmins($notification);
    }
    private function getSanitizedPath(): string
    {
        $section = $this->new_lecture->section;
        $course = $section->course;
        return sprintf(
            'uploads/%s/%s/%s',
            str_replace(' ', '-', strtolower($course->slug)),
            str_replace(' ', '-', strtolower($section->slug)),
            str_replace(' ', '-', strtolower($this->new_lecture->slug))
        );
    }

    private function updateModel($pathes, $newName)
    {
        // Log::info('pathes: ' . json_encode($pathes));

        ConvertedVideo::updateOrCreate(
            ['lecture_id' => $this->new_lecture->id],
            $pathes
        );

        $this->new_lecture->update([
            'video' => $newName,
            'processed' => true,
        ]);
    }

    // faild
    public function failed($exception)
    {
        Log::error('error from DuplicateLecture: ' . $exception->getMessage());
        Log::error('Exception Trace: ' . $exception->getTraceAsString());
        Log::error('getline: ' . $exception->getLine());
        $this->new_lecture->update(['processed' => -1]);

        $notification = new LectureStatusNotification($this->new_lecture->id, 0);
        AdminNotificationService::notifyAdmins($notification);
    }
}
