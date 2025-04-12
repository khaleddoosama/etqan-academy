<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentOpinionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'student' => [
                'name' => $this->student->name,
                'slug' => $this->student->slug,
                'picture' => $this->student->picture_url
            ],
            'opinion' => $this->opinion,
            'rate' => $this->rate,
            'created_at' => $this->created_at,
        ];
    }
}
