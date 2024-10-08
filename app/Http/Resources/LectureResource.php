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
            'image' => $this->thumbnail_url,
            'hours' => $this->hours,
            'minutes' => $this->minutes,
            'seconds' => $this->seconds,
            'quality' => $this->quality,
        ];

        if ($this->withVideo) {
            $data['converted_videos'] = $this->converted_videos;
            $data['attachments'] = $this->attachments_url;
        }
        return $data;
    }

    public static function collection($resource, $withVideo = false)
    {
        return parent::collection($resource)->each(function ($resource) use ($withVideo) {
            $resource->withVideo = $withVideo;
        });
    }
}
