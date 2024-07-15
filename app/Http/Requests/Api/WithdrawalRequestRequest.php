<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class WithdrawalRequestRequest extends FormRequest
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
            'points' => 'required|numeric|min:1|max:' . auth()->user()->points,
            'wallet_phone' => 'required|confirmed',
            'password' => 'required|current_password:api',
        ];
    }

    public function messages(): array
    {
        return [
            'points.max' => 'Not enough points',
        ];
    }
}
