<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CourseInstallmentRequest extends FormRequest
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
            'name' => 'nullable|string|max:255',
            'number_of_installments' => 'required|integer|min:1',
            'installment_amounts' => 'required|array',
            'installment_amounts.*' => 'required|numeric',
            'installment_duration' => 'required|integer|min:0',
            'description' => 'nullable|string',
        ];
    }
}
