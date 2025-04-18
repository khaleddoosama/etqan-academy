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
            // 'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            // 'image' => $this->thumbnail_url,
            'hours' => $this->hours >= 10 ? $this->hours : '0' . $this->hours,
            'minutes' => $this->minutes >= 10 ? $this->minutes : '0' . $this->minutes,
            'seconds' => $this->seconds >= 10 ? $this->seconds : '0' . $this->seconds,
            'views' => $this->views->where('user_id', auth('api')->id())->first()->views ?? 0,
            'is_free' => $this->is_free,
            // 'quality' => $this->quality,
        ];

        if ($this->withVideo) {
            // $data['converted_videos'] = $this->converted_videos;
            $data['video'] = $this->video_url;
            $data['attachments'] = $this->attachments_url;
            $data['description'] = $this->description;
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
