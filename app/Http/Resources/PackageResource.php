<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PackageResource extends JsonResource
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
            'slug' => $this->slug,
            'name' => $this->title,
            'description' => $this->description,
            'programs' => $this->programs ? ProgramResource::collection($this->programs()) : [],
            'plans' => $this->packagePlans ? PackagePlanResource::collection($this->packagePlans) : [],
        ];
    }
}
