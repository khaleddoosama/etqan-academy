<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StudentWorkRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_work_category_id' => 'required|exists:student_work_categories,id',
            'pathes' => 'required|array',
            'pathes.*' => 'required|file|max:30720', // max 30 mb
        ];
    }
}
