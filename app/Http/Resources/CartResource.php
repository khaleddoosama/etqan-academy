<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'total_price' => $this->total_price,
            'course' => [
                'slug' => $this->course->slug,
                'title' => $this->course->title,
                'price' => $this->course->price,
                'discount_price' => $this->course->discount_price,
                'image' => $this->course->thumbnail_url,
                'installments' => $this->course->courseInstallments ? CourseInstallmentResource::collection($this->course->courseInstallments) : [],
            ],
        ];
    }
}
