<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PackagePlanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'from' => $this->from,
            'price' => $this->price,
            'duration' => $this->duration,
            'device_limit' => $this->device_limit,
            'description' => $this->description,
            'programs' => $this->programs ? ProgramResource::collection($this->programs()) : [],
            'logo' => $this->logo_url,
        ];
    }
}
