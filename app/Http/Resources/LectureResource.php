<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class LectureResource extends JsonResource
{
    protected $withVideo;

    public function __construct($resource, $withVideo = true)
    {
        parent::__construct($resource);
        $this->withVideo = $withVideo;
    }

    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'image' => $this->thumbnail,
            'hours' => $this->hours,
            'minutes' => $this->minutes,
            'seconds' => $this->seconds,
            'quality' => $this->quality,
        ];

        if ($this->withVideo) {
            $data['converted_videos'] = $this->converted_videos;
        }
        return $data;
    }

    public static function collection($resource, $withVideo = false)
    {
        return tap(parent::collection($resource), function ($collection) use ($withVideo) {
            $collection->collection->each->withVideo = $withVideo;
        });
    }
}
