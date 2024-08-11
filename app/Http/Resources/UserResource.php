<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'slug' => $this->slug,
            'job_title' => $this->job_title,
            'email' => $this->email,
            'phone' => $this->phone,
            'picture' => $this->picture_url,
            'age' => $this->age,
            'code' => $this->code,
            'last_login' => $this->last_login,
            'points' => $this->points,
            'country' => $this->country,
            'image' => $this->picture_url,
            'courses_count' => $this->coursesCount(),
            'status' => $this->status,
        ];
    }
}
