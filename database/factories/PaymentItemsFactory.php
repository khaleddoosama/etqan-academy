<?php

namespace Database\Factories;

use App\Enums\PaymentType;
use App\Models\PaymentItems;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentItemsFactory extends Factory
{
    protected $model = PaymentItems::class;

    public function definition(): array
    {
        return [
            'course_id' => null,
            'course_installment_id' => null,
            'package_plan_id' => null,
            'payment_id' => null,
            'payment_type' => PaymentType::CASH,
            'amount' => 900,
        ];
    }
}

