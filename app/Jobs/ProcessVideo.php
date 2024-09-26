<?php

namespace App\Jobs;

use App\Notifications\LectureStatusNotification;
use App\Services\AdminNotificationService;
use FFMpeg\FFProbe;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use FFMpeg\Format\Video\X264;
use FFMpeg\Format\Video\WebM;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;

class ProcessVideo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $lecture;
    public $videoPath;
    public $i;
    public $index;

    public function __construct($lecture)
    {
        $this->lecture = $lecture;
    }

    public function handle(): void
    {

        $this->videoPath = $this->downloadVideoLocally(Storage::disk($this->lecture->disk)->url($this->lecture->video));
        // $videoPath = $this->getVideoPath();

        $video1 = $this->getVideoStream($this->videoPath);
        [$width, $height] = $this->getVideoDimensions($video1);
        $durationInSeconds = $this->getVideoDuration($this->videoPath);
        [$hours, $minutes, $seconds] = $this->convertDuration($durationInSeconds);
        $quality = $this->determineQualityAndConvert($width, $height);


        // نفذ FinalizeVideoProcessing بعد انتهاء جميع وظائف التحويل
        $conversionJobs = $this->collectConversionJobs($durationInSeconds);
        if (!empty($conversionJobs)) {
            $finalizeJob = new FinalizeVideoProcessing($this->lecture, $hours, $minutes, $seconds, $quality, $this->videoPath);
            $conversionJobs[] = $finalizeJob->onQueue('low')->delay(now()->addSeconds(20));
            Bus::chain($conversionJobs)->dispatch();
        }

        // dispatch(new FinalizeVideoProcessing($this->lecture, $hours, $minutes, $seconds, $quality, $this->videoPath))
        //     ->delay(now()->addSeconds(10));
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
        $response = Http::withOptions(['sink' => $file])->get($url);
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

    private function getVideoStream(string $videoPath)
    {
        $ffprobe = FFProbe::create();
        return $ffprobe->streams($videoPath)->videos()->first();
    }

    private function getVideoDimensions($video1): array
    {
        $width = $video1->get('width');
        $height = $video1->get('height');
        return [$width, $height];
    }

    private function getVideoDuration($videoPath): int
    {
        try {
            $ffprobe = FFProbe::create();
            $durationInSeconds = $ffprobe->format($videoPath)->get('duration');
            return (int) $durationInSeconds;
        } catch (\Exception $e) {
            Log::error("Error getting video duration: " . $e->getMessage());
            return 0;
        }
    }

    private function convertDuration(int $durationInSeconds): array
    {
        $hours = floor($durationInSeconds / 3600);
        $minutes = floor(($durationInSeconds / 60) % 60);
        $seconds = floor($durationInSeconds % 60);
        return [$hours, $minutes, $seconds];
    }

    private function determineQualityAndConvert(int $width, int $height): int
    {
        $quality = 0;
        if ($width > $height) {
            $quality = $this->convertVideoBasedOnResolution($width, $height, false);
        } elseif ($width < $height) {
            DB::transaction(function () {
                $this->lecture->update(['longitudinal' => true]);
            });
            $quality = $this->convertVideoBasedOnResolution($width, $height, true);
        }
        return $quality;
    }

    private function convertVideoBasedOnResolution(int $width, int $height, bool $isPortrait): int
    {
        $resolutions = [
            ['width' => 1920, 'height' => 1080, 'quality' => 1080],
            ['width' => 1280, 'height' => 720, 'quality' => 720],
            ['width' => 854, 'height' => 480, 'quality' => 480],
            ['width' => 640, 'height' => 360, 'quality' => 360],
            ['width' => 426, 'height' => 240, 'quality' => 240]
        ];

        foreach ($resolutions as $index => $resolution) {
            // Log::info("index: " . $index . ' ' . $width . ' ' . $height . ' ' . $resolution['width'] . ' ' . $resolution['height']);
            if ($isPortrait) {
                if ($width >= $resolution['height'] && $height >= $resolution['width']) {
                    // $this->dispatchConversionJobs($index);
                    $this->index = $index;
                    return $resolution['quality'];
                }
            } else {
                if ($width >= $resolution['width'] && $height >= $resolution['height']) {
                    // $this->dispatchConversionJobs($index);
                    $this->index = $index;
                    return $resolution['quality'];
                }
            }
        }
        return 0;
    }

    private function collectConversionJobs($durationInSeconds)
    {
        $formats = [
            [(new X264('aac', 'libx264'))->setKiloBitrate(4096), (new WebM('libvorbis', 'libvpx'))->setKiloBitrate(4096)],
            [(new X264('aac', 'libx264'))->setKiloBitrate(2048), (new WebM('libvorbis', 'libvpx'))->setKiloBitrate(2048)],
            [(new X264('aac', 'libx264'))->setKiloBitrate(750), (new WebM('libvorbis', 'libvpx'))->setKiloBitrate(750)],
            [(new X264('aac', 'libx264'))->setKiloBitrate(500), (new WebM('libvorbis', 'libvpx'))->setKiloBitrate(500)],
            [(new X264('aac', 'libx264'))->setKiloBitrate(300), (new WebM('libvorbis', 'libvpx'))->setKiloBitrate(300)]
        ];

        $videoWidths = [1920, 1280, 854, 640, 426];
        $videoHeights = [1080, 720, 480, 360, 240];
        $names = [
            [$this->getFileName($this->lecture->video, 'mp4', '1080p'), $this->getFileName($this->lecture->video, 'webm', '1080p')],
            [$this->getFileName($this->lecture->video, 'mp4', '720p'), $this->getFileName($this->lecture->video, 'webm', '720p')],
            [$this->getFileName($this->lecture->video, 'mp4', '480p'), $this->getFileName($this->lecture->video, 'webm', '480p')],
            [$this->getFileName($this->lecture->video, 'mp4', '360p'), $this->getFileName($this->lecture->video, 'webm', '360p')],
            [$this->getFileName($this->lecture->video, 'mp4', '240p'), $this->getFileName($this->lecture->video, 'webm', '240p')]
        ];

        $conversionJobs = [];

        for ($this->i = $this->index; $this->i < count($formats); $this->i++) {
            // Log::info("this->i: " . $this->i);
            for ($j = 0; $j < count($formats[$this->i]); $j++) {
                // Log::info("j: " . $j);
                $job = new ConvertSingleVideoFormat(
                    $this->lecture,
                    $formats[$this->i][$j],
                    $videoWidths[$this->i],
                    $videoHeights[$this->i],
                    $names[$this->i][$j],
                    $durationInSeconds,
                    $this->videoPath
                );

                $conversionJobs[] = $job->onQueue('low')->delay(now()->addSeconds(10));
            }
        }
        return $conversionJobs;
    }

    private function getFileName($fileName, $type, $quality)
    {
        return preg_replace('/\\.[^.\\s]{3,4}$/', '', $fileName) . '-' . $quality . '.' . $type;
    }

    // faild
    public function failed($exception)
    {
        DB::rollBack();

        Log::error('error from processVideo: ' . $exception->getMessage());
        Log::error('Exception Trace: ' . $exception->getTraceAsString());
        Log::error('getline: ' . $exception->getLine());
        DB::transaction(function () {
            $this->lecture->update(['processed' => -1]);
        });

        $notification = new LectureStatusNotification($this->lecture->id, 0);
        AdminNotificationService::notifyAdmins($notification, ['course.list', 'course.show']);
    }
}
