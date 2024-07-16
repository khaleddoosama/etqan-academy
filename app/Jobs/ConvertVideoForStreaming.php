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
        $ffprobe = FFProbe::create();
        $videoPath = storage_path('app/public/' . $this->lecture->video);
        $video1 = $ffprobe->streams($videoPath)->videos()->first();

        $width = $video1->get('width');
        $height = $video1->get('height');

        $media = FFMpeg::fromDisk($this->lecture->disk)->open($this->lecture->video);
        $durationInSeconds = $media->getDurationInSeconds();
        $hours = floor($durationInSeconds / 3600);
        $minutes = floor(($durationInSeconds / 60) % 60);
        $seconds = floor($durationInSeconds % 60);

        $quality = 0;
        // if video is landscape
        if ($width > $height) {
            if ($width >= 1920 && $height >= 1080) {
                $quality = 1080;
                $this->convertVideo(0);
            } elseif ($width >= 1280 && $height >= 720) {
                $quality = 720;
                $this->convertVideo(1);
            } elseif ($width >= 854 && $height >= 480) {
                $quality = 480;
                $this->convertVideo(2);
            } elseif ($width >= 640 && $height >= 360) {
                $quality = 360;
                $this->convertVideo(3);
            } elseif ($width >= 426 && $height >= 240) {
                $quality = 240;
                $this->convertVideo(4);
            }
        } elseif ($width < $height) { // if video is portrait
            $this->lecture->update(['longitudinal' => true]);
            if ($width >= 1080 && $height >= 1920) {
                $quality = 1080;
                $this->convertVideo(0);
            } elseif ($width >= 720 && $height >= 1280) {
                $quality = 720;
                $this->convertVideo(1);
            } elseif ($width >= 480 && $height >= 854) {
                $quality = 480;
                $this->convertVideo(2);
            } elseif ($width >= 360 && $height >= 640) {
                $quality = 360;
                $this->convertVideo(3);
            } elseif ($width >= 240 && $height >= 426) {
                $quality = 240;
                $this->convertVideo(4);
            }
        }
        Log::info('Video processed: ' . $this->lecture->video);
        // delete old video
        Storage::disk($this->lecture->disk)->delete($this->lecture->video);

        // $converted_video = new ConvertedVideo();

        // for ($i = 0; $i < count($this->names); $i++) {
        //     $converted_video->{'mp4_Format_' . $this->videoHeight[$i]} = $this->names[$i][0];
        //     $converted_video->{'webm_Format_' . $this->videoHeight[$i]} = $this->names[$i][1];
        // }

        // $converted_video->lecture_id = $this->lecture->id;
        // $converted_video->save();

        // update or create converted video
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
