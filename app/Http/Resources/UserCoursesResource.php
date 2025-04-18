<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserCoursesResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'course' => [
                'slug' => $this->course->slug,
                'title' => $this->course->title,
                'image' => $this->course->thumbnail_url,
                'category' => $this->course->category->name,
                'type' => $this->course->type,
            ],
            'is_completed' => $this->completed,
            'progress' => $this->progress,
            'created_at' => $this->created_at,
            'status' => $this->status,
            'review' => $this->review,
            'rating' => $this->rating
        ];
    }
}
