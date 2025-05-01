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
            'id' => $this->id,
            'title' => $this->title,
            'from' => $this->from,
            'price' => $this->price,
            'duration' => $this->duration,
            'duration_text' => $this->duration_text,
            'device_limit' => $this->device_limit,
            'number_of_downloads' => $this->number_of_downloads,
            'has_ai_access' => $this->has_ai_access,
            'has_flaticon_access' => $this->has_flaticon_access,
            'description' => $this->description,
            'programs' => $this->programs ? ProgramResource::collection($this->programs()) : [],
            'logo' => $this->logo_url,
        ];
    }
}
