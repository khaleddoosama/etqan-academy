<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class CourseResource extends JsonResource
{
    protected $is_collection;

    public function __construct($resource, $is_collection = true)
    {
        parent::__construct($resource);
        $this->is_collection = $is_collection;
    }
    public function toArray(Request $request): array
    {
        $data =  [
            'title' => $this->title,
            'slug' => $this->slug,
            'image' => $this->thumbnail_url,
            'category' => $this->category->name,
            'price' => $this->price,
            'discount_price' => $this->discount_price,
            'num_of_levels' => $this->number_of_levels_text,
            'programs' => $this->programs ? ProgramResource::collection($this->programs()) : [],
            'lessons' => $this->countLectures(),
            'total_duration' => $this->totalDuration(),
            'students_count' => $this->studentsCount(),
            'rating' => $this->rating,
            'instructor' => $this->instructor ? new InstructorResource($this->instructor) : null,
            // check if the user is enrolled in the course
            'is_enrolled' => auth('api')->check() && auth('api')->user()->isEnrolledInCourse($this->id),
            'offer' => $this->offer ? new OfferResource($this->offer) : null,
        ];

        if ($this->is_collection) {
            $data['description'] = $this->description;
            $data['sections'] = SectionResource::collection($this->sections);
            $data['installments'] = $this->courseInstallments ? CourseInstallmentResource::collection($this->courseInstallments) : [];
        }
        return $data;
    }

    public static function collection($resource, $is_collection = false)
    {
        return parent::collection($resource)->each(function ($resource) use ($is_collection) {
            $resource->is_collection = $is_collection;
        });
    }
}
