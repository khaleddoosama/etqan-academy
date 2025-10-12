<?php

namespace App\Http\Requests\Admin;

use App\Enums\PaymentStatusEnum;
use App\Enums\PaymentType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePaymentDetailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $paymentStatuses = array_map(static fn($status) => $status->value, PaymentStatusEnum::cases());
        $paymentTypes = array_map(static fn($type) => $type->value, PaymentType::cases());

        return [
            'user_id' => ['required', 'exists:users,id'],
            'gateway' => ['required', Rule::in(['fawaterak', 'instapay'])],
            'status' => ['required', Rule::in($paymentStatuses)],
            'invoice_id' => ['nullable', 'string', 'max:255'],
            'invoice_key' => ['nullable', 'string', 'max:255'],
            'payment_method' => ['nullable', 'string', 'max:255'],
            'payment_method_id' => ['nullable', 'string', 'max:255'],
            'transfer_number' => ['nullable', 'string', 'max:255'],
            'transfer_image' => ['nullable', 'file', 'mimes:jpeg,png,jpg,gif,pdf', 'max:2048'],
            'amount_before_coupon' => ['required', 'numeric', 'min:0'],
            'amount_after_coupon' => ['required', 'numeric', 'min:0'],
            'amount_confirmed' => ['required', 'numeric', 'min:0'],
            'coupon_id' => ['nullable', 'exists:coupons,id'],
            'paid_at' => ['required', 'date', 'before_or_equal:now'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.payment_type' => ['required', Rule::in($paymentTypes)],
            'items.*.amount' => ['required', 'numeric', 'min:0'],
            'items.*.course_id' => ['nullable', 'exists:courses,id'],
            'items.*.package_plan_id' => ['nullable', 'exists:package_plans,id'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $itemsInput = $this->input('items');

        if (is_array($itemsInput)) {
            $items = array_map(function ($item) {
                foreach (['course_id', 'package_plan_id'] as $key) {
                    if (isset($item[$key]) && $item[$key] === '') {
                        $item[$key] = null;
                    }
                }

                return $item;
            }, $itemsInput);

            $this->merge(['items' => $items]);
        }
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $items = $this->input('items', []);

            foreach ($items as $index => $item) {
                $selected = collect([
                    $item['course_id'] ?? null,
                    $item['package_plan_id'] ?? null,
                ])->filter();

                if ($selected->isEmpty()) {
                    $validator->errors()->add("items.$index.course_id", __('validation.required', ['attribute' => __('attributes.service')]));
                }

                if ($selected->count() > 1) {
                    $validator->errors()->add("items.$index.course_id", __('validation.custom.payment_items.single_service'));
                }

            }
        });
    }
}
