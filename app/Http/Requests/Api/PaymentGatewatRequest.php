<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class PaymentGatewatRequest extends FormRequest
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
            'payment_method_id' => 'required|integer',
            'cartItems' => 'required|array|min:1',
            'cartItems.*.name' => 'required|string',
            'cartItems.*.price' => 'required|numeric',
            'cartTotal' => 'required|numeric|min:1',
            'customer.first_name' => 'required|string',
            'customer.last_name' => 'required|string',
            'customer.email' => 'nullable|email',
            'customer.phone' => 'nullable|string',
            'customer.address' => 'nullable|string',
        ];
    }
}
