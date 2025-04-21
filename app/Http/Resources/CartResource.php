<?php

namespace App\Http\Resources;

use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'total_price' => $this->total_price,
            'course_installment' => new CourseInstallmentResource($this->courseInstallment),
            'course' => [
                'slug' => $this->course->slug,
                'title' => $this->course->title,
                'price' => $this->course->price,
                'discount_price' => $this->course->discount_price,
                'image' => $this->course->thumbnail_url,
            ],
        ];
    }
}
