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
            'course_installment' => [
                'id' => $this->course_installment_id,
                'name' => $this->courseInstallment->name,
                'number_of_installments' => $this->courseInstallment->number_of_installments,
                'installment_amounts' => $this->courseInstallment->installment_amounts,
                'installment_duration' => $this->courseInstallment->installment_duration
            ],
            'course' => [
                'slug' => $this->courseInstallment->course->slug,
                'title' => $this->courseInstallment->course->title,
                'price' => $this->courseInstallment->course->price,
                'discount_price' => $this->courseInstallment->course->discount_price,
                'image' => $this->courseInstallment->course->thumbnail_url,
            ],
        ];
    }
}
