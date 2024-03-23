<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'username' => $this->username,
            'slug' => $this->slug,
            'email' => $this->email,
            'phone' => $this->phone,
            'picture' => $this->picture,
            'status' => $this->status,
        ];
    }
}
