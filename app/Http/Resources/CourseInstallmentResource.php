<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseInstallmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'number_of_installments' => $this->number_of_installments,
            'installment_amounts' => $this->installment_amounts,
            'installment_duration' => $this->installment_duration,
            'status' => $this->status,
        ];
    }
}
