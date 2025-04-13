<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StudentOpinionRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'student_id' => auth('api')->id(),
        ]);
    }

    public function rules(): array
    {
        return [
            'student_id' => 'required|exists:users,id',
            'course_slug' => 'nullable|exists:courses,slug',
            'opinion' => 'required|string',
            'rating' => 'nullable|integer|between:1,5',
        ];
    }
}
