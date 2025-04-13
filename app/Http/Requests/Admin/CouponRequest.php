<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CouponRequest extends FormRequest
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
            'code' => 'required|string|max:255|unique:coupons,code,' . $this->coupon . ',id',
            'discount' => 'required|numeric|min:0',
            'type' => 'required|in:percentage,fixed',
            'start_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:start_at',
            'usage_limit' => 'nullable|integer|min:1',
            'status' => 'boolean',
        ];
    }
}
