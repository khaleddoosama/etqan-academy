<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class InstapayPaymentRequest extends FormRequest
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
            // 'amount_confirmed' => 'required|numeric|min:0.01',
            'transfer_image' => 'required|image|mimes:jpeg,png,jpg,gif,pdf|max:10024', // 10MB max size
            'coupon_code' => 'nullable|exists:coupons,code',
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            // 'amount_confirmed.required' => 'The confirmed amount is required.',
            // 'amount_confirmed.numeric' => 'The confirmed amount must be a number.',
            // 'amount_confirmed.min' => 'The confirmed amount must be greater than 0.',
            'transfer_image.required' => 'The transfer image is required.',
            'transfer_image.image' => 'The transfer file must be an image.',
            'transfer_image.mimes' => 'The transfer image must be a file of type: jpeg, png, jpg, gif.',
            'transfer_image.max' => 'The transfer image may not be greater than 2MB.',
            'coupon_code.exists' => 'The selected coupon code is invalid.',
        ];
    }    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // No additional preparation needed
    }

    /**
     * Get the validated data with additional fields.
     */
    public function validated($key = null, $default = null)
    {
        $data = parent::validated();
        $data['user_id'] = auth('api')->id();
        return $data;
    }
}
