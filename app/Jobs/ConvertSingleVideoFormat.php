<?php

namespace App\Jobs;

use App\Models\ConvertedVideo;
use App\Notifications\LectureStatusNotification;
use App\Services\AdminNotificationService;
use FFMpeg\Coordinate\Dimension;
use FFMpeg\Filters\Video\VideoFilters;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use ProtoneMedia\LaravelFFMpeg\Filters\WatermarkFactory;
use Illuminate\Support\Facades\Http;

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
    }

    public function handle()
    {
        Log::info('Convert: ' . $this->videoPath);


        // Check if video exists
        if (!$this->videoPath) {
            Log::error('Video not found: ' . $this->videoPath);
            return;
        }

        $chunks = $this->splitVideoIntoChunks($this->videoPath, $this->durationInSeconds / 5);
        if (empty($chunks)) {
            Log::error('Failed to split video into chunks: ' . $this->videoPath);
            $this->videoPath = $this->downloadVideoLocally(Storage::disk($this->lecture->disk)->url($this->lecture->video));
            $chunks = $this->splitVideoIntoChunks($this->videoPath, $this->durationInSeconds / 5);
        }

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
                    '-preset',
                    'ultrafast', // Faster encoding preset
                    '-bufsize',
                    '512k', // Reduce buffer size
                ]);


            Log::info(message: 'Converted: ' . $chunkName);
            $processedChunks[] = storage_path('app/public/' . $chunkName);
            // Log::info('processedChunks: ' . json_encode($processedChunks));
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
        Storage::disk('s3')->put($outputName, fopen($outputPath, 'r+'));

        preg_match('/-(\d{3})p\.(\w+)$/', $this->name, $matches);
        if ($matches) {
            $resolution = $matches[1]; // "240"
            $extension = $matches[2];  // "mp4"
            $names = [
                $extension . '_Format_' . $resolution => $outputName
            ];
            DB::transaction(function () use ($names) {

                ConvertedVideo::updateOrCreate(
                    ['lecture_id' => $this->lecture->id],
                    $names
                );
            });
        } else {
            Log::error('Failed to extract resolution and extension from video name: ' . $this->name);
        }

        // Clean up temporary files
        foreach ($chunks as $chunk) {
            unlink($chunk);
        }
        unlink($fileList);
        unlink($outputPath);
    }
    private function downloadVideoLocally($url): ?string
    {
        $path = Storage::disk('public')->path($this->lecture->video);

        // Ensure the directory exists
        $directory = dirname($path);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $file = fopen($path, 'w');
        Log::info('Downloading video from URL: ' . $url);

        $response = Http::retry(3, 5000)
            ->timeout(600)
            ->withOptions(['sink' => $file])
            ->get($url);
        Log::info('Video downloaded to: ' . $path);

        if ($response->successful()) {
            Log::info('Video downloaded successfully');
            fclose($file);
            return str_replace('//', '/', $path);
        }

        Log::error("Failed to download video from URL: $url");
        fclose($file);
        return null;
    }

    public function failed($exception)
    {
        DB::rollBack();

        Log::error('error from ConvertSingleVideoFormat: ' . $exception->getMessage());
        Log::error('Exception Trace: ' . $exception->getTraceAsString());
        Log::error('getline: ' . $exception->getLine());

        DB::transaction(function () {
            $this->lecture->update(['processed' => -1]);
        });
        // $notification = new LectureStatusNotification($this->lecture->id, 0);
        // AdminNotificationService::notifyAdmins($notification, ['course.list', 'course.show']);
    }
}
