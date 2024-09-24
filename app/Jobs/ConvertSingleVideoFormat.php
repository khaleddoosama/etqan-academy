<?php

namespace App\Jobs;

use App\Notifications\LectureStatusNotification;
use App\Services\AdminNotificationService;
use FFMpeg\Coordinate\Dimension;
use FFMpeg\Filters\Video\VideoFilters;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use ProtoneMedia\LaravelFFMpeg\Filters\WatermarkFactory;

class ConvertSingleVideoFormat implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $lecture;
    protected $format;
    protected $videoWidth;
    protected $videoHeight;
    protected $name;
    protected $videoPath;
    protected $durationInSeconds;

    public function __construct($lecture, $format, $videoWidth, $videoHeight, $name, $durationInSeconds, $videoPath)
    {
        $this->lecture = $lecture;
        $this->format = $format;
        $this->videoWidth = $videoWidth;
        $this->videoHeight = $videoHeight;
        $this->name = $name;
        $this->durationInSeconds = $durationInSeconds;
        $this->videoPath = $videoPath;

        // update lecture status
        $this->lecture->update(['processed' => 0]);
    }

    public function handle()
    {
        Log::info('Convert: ' . $this->videoPath);

        // Check if video exists
        if (!$this->videoPath) {
            Log::error('Video not found: ' . $this->videoPath);
            return;
        }

        $chunks = $this->splitVideoIntoChunks($this->videoPath, $this->durationInSeconds / 10);
        $watermarkPath = asset('asset/logo-100.png');
        // Log::info('watermark: ' . $watermarkPath);

        foreach ($chunks as $index => $chunk) {
            $chunkName = $this->name . '_part' . $index . '.' . pathinfo($chunk, PATHINFO_EXTENSION);
            FFMpeg::openUrl($chunk)
                ->addFilter(function (VideoFilters $filters) {
                    $filters->resize(new Dimension($this->videoWidth, $this->videoHeight));
                })
                ->addWatermark(function (WatermarkFactory $watermark) use ($watermarkPath) {
                    $watermark->openUrl($watermarkPath)
                        ->horizontalAlignment(WatermarkFactory::RIGHT, 25)
                        ->verticalAlignment(WatermarkFactory::BOTTOM, 25);
                })
                ->export()
                ->toDisk('public')
                ->inFormat($this->format)
                ->save($chunkName, [
                    '-threads',
                    '1', // Reduce the number of threads used by FFMpeg
                    '-bufsize',
                    '64k', // Reduce buffer size
                ]);


            Log::info('Converted: ' . $chunkName);
            $processedChunks[] = storage_path('app/public/' . $chunkName);
        }
        // Remove the original chunks
        foreach ($chunks as $chunk) {
            unlink($chunk);
        }

        // Optionally merge the processed chunks back into a single video file
        $this->mergeChunks($processedChunks, $this->name);
    }

    private function splitVideoIntoChunks($videoPath, $chunkDuration = 60)
    {
        Log::info('chunkDuration: ' . $chunkDuration);
        $chunks = [];
        $outputDirectory = storage_path('app/public/video_chunks/');

        if (!file_exists($outputDirectory)) {
            mkdir($outputDirectory, 0755, true);
        }

        $command = "ffmpeg -i $videoPath -c copy -map 0 -segment_time $chunkDuration -f segment -reset_timestamps 1 {$outputDirectory}output%03d.mp4";

        exec($command);

        foreach (glob($outputDirectory . 'output*.mp4') as $chunk) {
            $chunks[] = $chunk;
        }

        return $chunks;
    }

    private function mergeChunks($chunks, $outputName)
    {
        $outputDirectory = storage_path('app/public/video_chunks/');
        $fileList = $outputDirectory . 'filelist.txt';

        $file = fopen($fileList, 'w');
        foreach ($chunks as $chunk) {
            fwrite($file, "file '$chunk'\n");
        }
        fclose($file);

        $outputPath = storage_path('app/public/' . $outputName);
        $command = "ffmpeg -f concat -safe 0 -i $fileList -c copy $outputPath";

        exec($command, $output, $return_var);

        if ($return_var !== 0) {
            Log::error('Error merging video chunks: ' . implode("\n", $output));
            return;
        }


        // Check if the merged file exists before uploading
        if (!file_exists($outputPath) || filesize($outputPath) === 0) {
            Log::error('Merged video file not found or is empty: ' . $outputPath);
            return;
        }

        // Upload the merged video to public storage
        $v = Storage::disk('s3')->put($outputName, fopen($outputPath, 'r+'));

        // Clean up temporary files
        foreach ($chunks as $chunk) {
            unlink($chunk);
        }
        unlink($fileList);
        unlink($outputPath);
    }


    public function failed($exception)
    {
        Log::error('error from ConvertSingleVideoFormat: ' . $exception->getMessage());
        Log::error('Exception Trace: ' . $exception->getTraceAsString());
        Log::error('getline: ' . $exception->getLine());
        $this->lecture->update(['processed' => -1]);

        $notification = new LectureStatusNotification($this->lecture->id, 0);
        AdminNotificationService::notifyAdmins($notification,['course.list','course.show']);
    }
}
