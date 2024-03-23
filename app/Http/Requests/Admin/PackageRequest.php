<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\AttributesTrait;
use Illuminate\Foundation\Http\FormRequest;

class PackageRequest extends FormRequest
{
    use AttributesTrait;

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'duration' => 'required|numeric',
            // 'discount' => 'required|numeric',
            'features' => 'required|array',
            'features.*' => 'required|string',
            'image' => 'image|max:2048' .  optional($this->package)->exists ? '' : '|required',
        ];
    }
}
