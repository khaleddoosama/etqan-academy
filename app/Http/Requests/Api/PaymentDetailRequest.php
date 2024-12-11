<?php

namespace App\Http\Requests\Api;

use App\Enums\PaymentMethod;
use App\Enums\PaymentType;
use App\Enums\Status;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaymentDetailRequest extends FormRequest
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
            'course_slug' => 'required|exists:courses,slug',
            'whatsapp_number' => 'required|string',
            'payment_type' => 'required|in:' . implode(',', array_column(PaymentType::cases(), 'value')),
            'payment_method' => 'required|in:' . implode(',', array_column(PaymentMethod::cases(), 'value')),
            'transfer_number' => [
                'nullable',
                'string',
                Rule::requiredIf($this->payment_method === 'wallet'),
            ],
            'transfer_image' => 'required|mimes:jpeg,png,jpg,gif,pdf|max:2048',
        ];
    }

    public function validated($key = null, $default = null)
    {
        $data = parent::validated();
        $data['user_id'] = auth()->user()->id;
        return $data;
    }
}
