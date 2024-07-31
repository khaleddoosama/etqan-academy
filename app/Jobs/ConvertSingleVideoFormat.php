<?php

namespace App\Jobs;

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

class ConvertSingleVideoFormat implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $lecture;
    protected $format;
    protected $videoWidth;
    protected $videoHeight;
    protected $name;
    protected $videoPath;

    public function __construct($lecture, $format, $videoWidth, $videoHeight, $name, $videoPath)
    {
        $this->lecture = $lecture;
        $this->format = $format;
        $this->videoWidth = $videoWidth;
        $this->videoHeight = $videoHeight;
        $this->name = $name;
        $this->videoPath = $videoPath;
    }

    public function handle()
    {


        Log::info('Convert: ' . $this->videoPath);
        // check if video exits
        if (!Storage::disk('public')->exists($this->videoPath)) {
            Log::error('Video not found: ' . $this->videoPath);
        }
        FFMpeg::openUrl($this->videoPath)
            ->export()
            ->toDisk('s3')
            ->inFormat($this->format)
            ->addFilter(function (VideoFilters $filters) {
                $filters->resize(new Dimension($this->videoWidth, $this->videoHeight));
            })
            ->save($this->name);
        Log::info('Converted: ' . $this->name);
    }

    // faild
    public function failed($exception)
    {
        Log::error('error from ConvertSingleVideoFormat: ' . $exception->getMessage());
        Log::error('Exception Trace: ' . $exception->getTraceAsString());
        Log::error('getline: ' . $exception->getLine());
        $this->lecture->update(['processed' => -1]);
    }
}
