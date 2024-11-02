<?php

namespace App\Services;

use App\Models\ConvertedVideo;
use App\Notifications\LectureStatusNotification;
use App\Services\AdminNotificationService;
use App\Services\AwsS3Service;
use FFMpeg\FFProbe;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class VideoProcessingService
{
    protected $awsS3Service;

    public function __construct(AwsS3Service $awsS3Service)
    {
        $this->awsS3Service = $awsS3Service;
    }

    public function videoExistsOnServer($lecture): bool
    {
        return $this->awsS3Service->fileExists($lecture->video);
    }

    public function getVideoUrl($lecture): string
    {
        return $this->awsS3Service->getFileUrl($lecture->video);
    }

    public function markLectureAsFailed($lecture): void
    {
        $lecture->update(['processed' => -1]);
    }

    public function notifyAdmins($lecture, int $status): void
    {
        $notification = new LectureStatusNotification($lecture->id, $status, $lecture->title);
        AdminNotificationService::notifyAdmins($notification, ['course.list', 'course.show']);
    }

    public function processVideo($lecture): array
    {
        $videoPath = $this->downloadVideoLocally($this->getVideoUrl($lecture), $lecture);

        if (!$videoPath) {
            $this->markLectureAsFailed($lecture);
            return [];
        }

        $videoStream = $this->getVideoStream($videoPath);
        if (!$videoStream) {
            $this->markLectureAsFailed($lecture);
            return [];
        }

        $dimensions = $this->getVideoDimensions($videoStream);
        $duration = $this->getVideoDuration($videoPath);

        return [
            'hours' => floor($duration / 3600),
            'minutes' => floor(($duration / 60) % 60),
            'seconds' => floor($duration % 60),
            'quality' => $this->determineQualityAndConvert($lecture, $dimensions['width'], $dimensions['height'])
        ];
    }

    private function downloadVideoLocally(string $url, $lecture): ?string
    {
        $tempPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid() . '_' . basename($lecture->video);
        Log::info('Downloading video from URL: ' . $url);

        try {
            $this->awsS3Service->downloadFile($lecture->video, $tempPath);
            Log::info('Video downloaded successfully to: ' . $tempPath);
            return str_replace('//', '/', $tempPath);
        } catch (\Exception $e) {
            Log::error("Failed to download video from S3: " . $e->getMessage());
            return null;
        }
    }

    private function getVideoStream(string $videoPath)
    {
        try {
            $ffprobe = FFProbe::create();
            return $ffprobe->streams($videoPath)->videos()->first();
        } catch (\Exception $e) {
            Log::error("Error getting video stream: " . $e->getMessage());
            return null;
        }
    }

    private function getVideoDimensions($videoStream): array
    {
        return [
            'width' => $videoStream->get('width'),
            'height' => $videoStream->get('height')
        ];
    }

    private function getVideoDuration(string $videoPath): int
    {
        try {
            $ffprobe = FFProbe::create();
            return (int)$ffprobe->format($videoPath)->get('duration');
        } catch (\Exception $e) {
            Log::error("Error getting video duration: " . $e->getMessage());
            return 0;
        }
    }

    private function determineQualityAndConvert($lecture, int $width, int $height): int
    {
        if ($width > $height) {
            return $this->convertVideoBasedOnResolution($width, $height, false);
        } elseif ($width < $height) {
            $lecture->update(['longitudinal' => true]);
            return $this->convertVideoBasedOnResolution($width, $height, true);
        }
        return 0;
    }

    private function convertVideoBasedOnResolution(int $width, int $height, bool $isPortrait): int
    {
        $resolutions = [
            ['width' => 1280, 'height' => 720, 'quality' => 720],
            ['width' => 854, 'height' => 480, 'quality' => 480],
            ['width' => 640, 'height' => 360, 'quality' => 360],
            ['width' => 426, 'height' => 240, 'quality' => 240]
        ];

        // Ensure the dimensions match the expected orientation.
        list($maxDim, $minDim) = $isPortrait ? [$height, $width] : [$width, $height];

        foreach ($resolutions as $resolution) {
            if ($minDim >= $resolution['height'] && $maxDim >= $resolution['width']) {
                return $resolution['quality'];
            }
        }

        return 0;
    }
}
