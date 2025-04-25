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
            'course_attachments_link' => 'nullable|string',
            'type' => 'required|in:separated,comprehensive',
            'instructor_id' => 'nullable|exists:instructors,id',
            'programs[]' => 'nullable|array',
            'programs.*' => 'nullable|exists:programs,id',
            'thumbnail' => 'nullable|image|max:2048', // max 2mb
            'diploma_details_file' => 'nullable|file|max:10024', // max 10mb
            'sections' => 'required|array',
            'sections.*.title' => 'nullable|string|max:255',
            'sections.*.description' => 'nullable|string',
            'sections.*.id' => 'nullable|exists:sections,id',
        ];
    }
}
