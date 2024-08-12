<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class RequestCourseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'course_id' => 'required|exists:courses,id',
            'phone' => 'required|string',
            'message' => 'nullable|string',

        ];
    }

    public function validated($key = null, $default = null)
    {
        $data = parent::validated();
        $data['student_id'] = auth()->user()->id;
        return $data;
    }

}
