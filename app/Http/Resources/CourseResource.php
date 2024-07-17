<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'image' => $this->thumbnail,
            'category' => $this->category->name,
            'price' => $this->price,
            'num_of_levels' => $this->number_of_levels,
            'programs' => $this->programs ? ProgramResource::collection($this->programs()) : [],
            'sections' => SectionResource::collection($this->sections),
            'lessons' => $this->countLectures(),
            'total_duration' => $this->totalDuration(),
            'students_count' => $this->students_count,
            'instructor' => $this->instructor ? new InstructorResource($this->instructor) : null
        ];
    }
}
