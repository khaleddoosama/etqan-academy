<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\AttributesTrait;
use Illuminate\Foundation\Http\FormRequest;

class CourseRequest extends FormRequest
{
    use AttributesTrait;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric',
            'discount_price' => 'nullable|numeric',
            'number_of_levels' => 'required|integer',
            'instructor_id' => 'nullable|exists:instructors,id',
            'programs[]' => 'nullable|array',
            'programs.*' => 'nullable|exists:programs,id',
            'thumbnail' => 'nullable|image|max:2048',
            'sections' => 'required|array',
            'sections.*.title' => 'required|string|max:255',
            'sections.*.description' => 'required|string',
            'sections.*.id' => 'nullable|exists:sections,id',
        ];
    }
}
