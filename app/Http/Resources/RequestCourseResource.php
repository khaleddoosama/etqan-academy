<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RequestCourseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'phone' => $this->phone,
            'message' => $this->message,
            'status' => $this->status,
            'course' => new CourseResource($this->course),
            'student' => $this->student ? new UserResource($this->student) : null,
            'created_at' => Carbon::parse($this->created_at)->diffForHumans(),
        ];
    }
}
