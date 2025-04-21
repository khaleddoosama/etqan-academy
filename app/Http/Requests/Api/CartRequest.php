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
            'course_slug' => 'required|exists:courses,slug',
        ];
    }

    // if course_installment_id is exist then must check its belongs to the course
    // public function withValidator($validator)
    // {
    //     $validator->after(function ($validator) {
    //         if ($this->course_installment_id) {
    //             $courseInstallment = CourseInstallment::find($this->course_installment_id);
    //             if ($courseInstallment->course_id != $this->course_id) {
    //                 $validator->errors()->add('course_installment_id', 'The selected course installment is invalid.');
    //             }
    //         }
    //     });
    // }
}
