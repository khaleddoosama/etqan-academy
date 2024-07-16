<?php

namespace App\Jobs;

use App\Models\ConvertedVideo;
use FFMpeg\Coordinate\Dimension;
use FFMpeg\FFProbe;
use FFMpeg\Filters\Video\VideoFilters;
use FFMpeg\Format\Video\WebM;
use FFMpeg\Format\Video\X264;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

class ConvertVideoForStreaming implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public  $lecture;
    public $format;
    public $videoWidth;
    public $videoHeight;

    public $i;
    public $names;
    public function __construct($lecture)
    {
        $this->lecture = $lecture;
    }


    // convert video
    public function convertVideo($loopNumber)
    {
        $this->format = array(
            //1080
            array(
                (new X264('aac', 'libx264'))->setKiloBitrate(4096), (new WebM('libvorbis', 'libvpx'))->setKiloBitrate(4096),
            ),

            //720
            array(
                (new X264('aac', 'libx264'))->setKiloBitrate(2048), (new WebM('libvorbis', 'libvpx'))->setKiloBitrate(2048),
            ),

            //480
            array(
                (new X264('aac', 'libx264'))->setKiloBitrate(750), (new WebM('libvorbis', 'libvpx'))->setKiloBitrate(750),
            ),

            //360
            array(
                (new X264('aac', 'libx264'))->setKiloBitrate(500), (new WebM('libvorbis', 'libvpx'))->setKiloBitrate(500),
            ),

            //240
            array(
                (new X264('aac', 'libx264'))->setKiloBitrate(300), (new WebM('libvorbis', 'libvpx'))->setKiloBitrate(300),
            )
        );

        $this->videoWidth = array(1920, 1280, 854, 640, 426);
        $this->videoHeight = array(1080, 720, 480, 360, 240);

        $this->names = array(
            array(
                // '1080p-' . $this->getFileName($this->lecture->video, 'mp4'), '1080p-' . $this->getFileName($this->lecture->video, 'webm'),
                $this->getFileName($this->lecture->video, 'mp4', '1080p'), $this->getFileName($this->lecture->video, 'webm', '1080p'),
            ),

            array(
                // '720p-' . $this->getFileName($this->lecture->video, 'mp4'), '720p-' . $this->getFileName($this->lecture->video, 'webm'),
                $this->getFileName($this->lecture->video, 'mp4', '720p'), $this->getFileName($this->lecture->video, 'webm', '720p'),
            ),

            array(
                // '480p-' . $this->getFileName($this->lecture->video, 'mp4'), '480p-' . $this->getFileName($this->lecture->video, 'webm'),
                $this->getFileName($this->lecture->video, 'mp4', '480p'), $this->getFileName($this->lecture->video, 'webm', '480p'),
            ),

            array(
                // '360p-' . $this->getFileName($this->lecture->video, 'mp4'), '360p-' . $this->getFileName($this->lecture->video, 'webm'),
                $this->getFileName($this->lecture->video, 'mp4', '360p'), $this->getFileName($this->lecture->video, 'webm', '360p'),
            ),

            array(
                // '240p-' . $this->getFileName($this->lecture->video, 'mp4'), '240p-' . $this->getFileName($this->lecture->video, 'webm'),
                $this->getFileName($this->lecture->video, 'mp4', '240p'), $this->getFileName($this->lecture->video, 'webm', '240p'),
            )
        );

        for ($this->i = $loopNumber; $this->i < count($this->format); $this->i++) {
            for ($j = 0; $j < count($this->format[$this->i]); $j++) {
                FFMpeg::fromDisk($this->lecture->disk)
                    ->open($this->lecture->video)
                    ->export()
                    ->toDisk(env('FILESYSTEM_DISK'))
                    ->inFormat($this->format[$this->i][$j])
                    ->addFilter(function (VideoFilters $filters) {
                        $filters->resize(new Dimension($this->videoWidth[$this->i], $this->videoHeight[$this->i]));
                    })
                    ->save($this->names[$this->i][$j]);
            }
        }
    }
    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $videoPath = $this->getVideoPath();
        $video1 = $this->getVideoStream($videoPath);

        list($width, $height) = $this->getVideoDimensions($video1);
        $durationInSeconds = $this->getVideoDuration();
        list($hours, $minutes, $seconds) = $this->convertDuration($durationInSeconds);

        $quality = $this->determineQualityAndConvert($width, $height);

        $this->logVideoProcessing();
        // $this->deleteOldVideo();
        $this->updateConvertedVideo();

        $this->updateLecture($hours, $minutes, $seconds, $quality);
    }

    private function getVideoPath(): string
    {
        return storage_path('app/public/' . $this->lecture->video);
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

    private function getVideoDuration(): int
    {
        $media = FFMpeg::fromDisk($this->lecture->disk)->open($this->lecture->video);
        return $media->getDurationInSeconds();
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
            $this->lecture->update(['longitudinal' => true]);
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
            if ($isPortrait) {
                if ($width >= $resolution['height'] && $height >= $resolution['width']) {
                    $this->convertVideo($index);
                    return $resolution['quality'];
                }
            } else {
                if ($width >= $resolution['width'] && $height >= $resolution['height']) {
                    $this->convertVideo($index);
                    return $resolution['quality'];
                }
            }
        }
        return 0;
    }

    private function logVideoProcessing()
    {
        Log::info('Video processed: ' . $this->lecture->video);
    }

    private function deleteOldVideo()
    {
        Storage::disk($this->lecture->disk)->delete($this->lecture->video);
    }

    private function updateConvertedVideo()
    {
        ConvertedVideo::updateOrCreate(
            ['lecture_id' => $this->lecture->id],
            [
                'mp4_Format_1080' => $this->names[0][0],
                'webm_Format_1080' => $this->names[0][1],
                'mp4_Format_720' => $this->names[1][0],
                'webm_Format_720' => $this->names[1][1],
                'mp4_Format_480' => $this->names[2][0],
                'webm_Format_480' => $this->names[2][1],
                'mp4_Format_360' => $this->names[3][0],
                'webm_Format_360' => $this->names[3][1],
                'mp4_Format_240' => $this->names[4][0],
                'webm_Format_240' => $this->names[4][1],
            ]
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

    public function getFileName($fileName, $type, $quality)
    {
        // return preg_replace('/\\.[^.\\s]{3,4}$/', '', $fileName) . '.' . $type;
        return preg_replace('/\\.[^.\\s]{3,4}$/', '', $fileName) . '-' . $quality . '.' . $type;
    }


    // faild
    public function failed($exception)
    {
        Log::error('error: ' . $exception->getMessage());
        Log::error('Exception Trace: ' . $exception->getTraceAsString());
        Log::error('getline: ' . $exception->getLine());
        $this->lecture->update(['processed' => -1]);
    }
}
