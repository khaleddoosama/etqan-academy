<?php

namespace App\Jobs;

use App\Models\ConvertedVideo;
use Exception;
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
use Illuminate\Support\Facades\Http;

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
        Log::info('names: ' . json_encode($this->names));
        // Resolve the real path to avoid issues with the path formatting
        $videoPath = Storage::disk($this->lecture->disk)->path($this->lecture->video);


        for ($this->i = $loopNumber; $this->i < count($this->format); $this->i++) {
            for ($j = 0; $j < count($this->format[$this->i]); $j++) {
                Log::info('Convert: ' . $videoPath);
                $ffmpeg = FFMpeg::openUrl($videoPath)
                    ->export()
                    ->toDisk('s3')
                    ->inFormat($this->format[$this->i][$j])
                    ->addFilter(function (VideoFilters $filters) {
                        $filters->resize(new Dimension($this->videoWidth[$this->i], $this->videoHeight[$this->i]));
                    })
                    ->save($this->names[$this->i][$j]);
                // Log::info('Ffmpeg: ' . $ffmpeg->getFFMpegBinary());

                // $ffmpeg
                //     ->export()
                //     ->toDisk(env('FILESYSTEM_DISK'))
                //     ->inFormat($this->format[$this->i][$j])
                //     ->addFilter(function (VideoFilters $filters) {
                //         $filters->resize(new Dimension($this->videoWidth[$this->i], $this->videoHeight[$this->i]));
                //     })
                //     ->save($this->names[$this->i][$j]);

                Log::info('Converted: ' . $this->names[$this->i][$j]);
            }
        }
    }
    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Processing video: ' . $this->lecture->video);
        $videoPath = $this->getVideoPath();
        Log::info('Video path: ' . $videoPath);

        if (file_exists($videoPath)) {
            Log::info("File exists: " . $videoPath);
        } else {
            Log::error("File does not exist: " . $videoPath);
        }

        $videoPath_real = realpath($videoPath);
        Log::info("Absolute path: " . $videoPath_real);
        if (file_exists($videoPath_real)) {
            Log::info("File exist2: " . $videoPath_real);
        } else {
            Log::error("File does not exists2: " . $videoPath_real);
        }



        $videoPath_without_app = str_replace('app/', '', $videoPath);
        Log::info("Without app: " . $videoPath_without_app);
        if (file_exists($videoPath_without_app)) {
            Log::info("File exists3: " . $videoPath_without_app);
        } else {
            Log::error("File does not exists3: " . $videoPath_without_app);
        }

        $videoPath_without_app_public = str_replace('app/public/', '', $videoPath);
        Log::info("Without app and public: " . $videoPath_without_app_public);
        if (file_exists($videoPath_without_app_public)) {
            Log::info("File exists4: " . $videoPath_without_app_public);
        } else {
            Log::error("File does not exists4: " . $videoPath_without_app_public);
        }

        $videoPath_without_public = $this->lecture->video;
        Log::info("Without public: " . $videoPath_without_public);
        if (file_exists($videoPath_without_public)) {
            Log::info("File exists5: " . $videoPath_without_public);
        } else {
            Log::error("File does not exists5: " . $videoPath_without_public);
        }

        $videoPath_with_asset = asset($this->lecture->video);
        Log::info("Without asset: " . $videoPath_with_asset);
        if (file_exists($videoPath_with_asset)) {
            Log::info("File exists6: " . $videoPath_with_asset);
        } else {
            Log::error("File does not exists6: " . $videoPath_with_asset);
        }

        $this->downloadVideoLocally($videoPath_with_asset);
        if (Storage::disk($this->lecture->disk)->exists($this->lecture->video)) {
            Log::info("File exists7: " . Storage::disk($this->lecture->disk)->path($this->lecture->video));
        } else {
            Log::error("File does not exists7: " . Storage::disk($this->lecture->disk)->path($this->lecture->video));
        }
        $p = Storage::disk($this->lecture->disk)->path($this->lecture->video);
        Log::info('Video path: ' . $p);


        $video1 = $this->getVideoStream($p);
        Log::info('Video stream: ');

        list($width, $height) = $this->getVideoDimensions($video1);
        Log::info('Video dimensions: ' . $width . 'x' . $height);

        $durationInSeconds = $this->getVideoDuration($p);
        Log::info('Video duration: ' . $durationInSeconds . ' seconds');
        list($hours, $minutes, $seconds) = $this->convertDuration($durationInSeconds);
        Log::info('Video duration: ' . $hours . ' hours, ' . $minutes . ' minutes, ' . $seconds . ' seconds');
        $quality = $this->determineQualityAndConvert($width, $height);
        Log::info('Video quality: ' . $quality);
        // $this->logVideoProcessing();
        $this->deleteOldVideo();
        $this->updateConvertedVideo();
        Log::info('Video processed: ' . $this->lecture->video);
        $this->updateLecture($hours, $minutes, $seconds, $quality);
        Log::info('Lecture updated: ' . $this->lecture->id);
    }

    private function getVideoPath(): string
    {
        // return storage_path('app/public/' . $this->lecture->video);
        $path = public_path($this->lecture->video);
        // enusure there are no //
        return str_replace('//', '/', $path);
    }

    private function downloadVideoLocally($url)
    {
        // Download the video file from the URL
        $response = Http::get($url);

        // Save the file to the local path
        Storage::disk($this->lecture->disk)->put($this->lecture->video, $response->body());
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

            // Check if the file exists
            if (!file_exists($videoPath)) {
                Log::error("File not found: " . $videoPath);
                return 0; // Or handle this scenario appropriately
            }

            // Create an FFProbe instance
            $ffprobe = FFProbe::create();

            // Retrieve the duration of the video
            $durationInSeconds = $ffprobe->format($videoPath)->get('duration');

            return (int) $durationInSeconds;
        } catch (\Exception $e) {
            Log::error("Error getting video duration: " . $e->getMessage());
            return 0; // Return 0 or handle the error appropriately
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
        // Storage::disk($this->lecture->disk)->delete($this->lecture->video);
        // unlink($this->getVideoPath());
        if (file_exists($this->getVideoPath())) {
            unlink($this->getVideoPath());
        }
        if (Storage::disk($this->lecture->disk)->exists($this->lecture->video)) {
            Storage::disk($this->lecture->disk)->delete($this->lecture->video);
        }
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
