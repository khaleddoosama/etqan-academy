<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\AttributesTrait;
use App\Rules\BadWordCheck;
use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    use AttributesTrait;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|unique:categories,name,' . optional($this->category)->id,
        ];
    }
}
