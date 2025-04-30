<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CartRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'user_id' => auth('api')->id(),
        ]);
    }
    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'course_installment_id' => 'nullable|exists:course_installments,id',
            'course_slug' => 'nullable|exists:courses,slug',
            'package_plan_id' => 'nullable|exists:package_plans,id',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (!$this->course_slug && !$this->package_plan_id) {
                $validator->errors()->add('course_slug', 'Either course_slug or package_plan_id must be provided.');
                $validator->errors()->add('package_plan_id', 'Either course_slug or package_plan_id must be provided.');
            }
        });
    }
}
