<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\AttributesTrait;
use Illuminate\Foundation\Http\FormRequest;

class SubcategoryRequest extends FormRequest
{
    use AttributesTrait;

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'name' => 'required|string|unique:subcategories,name,' . optional($this->subcategory)->id,
            'category_id' => 'required|exists:categories,id',
        ];
    }
}
