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
            'thumbnail' => 'nullable|image|max:2048',
            'sections' => 'required|array',
            'sections.*.title' => 'required|string|max:255',
            'sections.*.description' => 'required|string',
            'sections.*.id' => 'nullable|exists:sections,id',
        ];
    }


    public function validated($key = null, $default = null)
    {
        $data = parent::validated();
        $data['instructor_id'] = auth()->user()->id;

        return $data;
    }
}
