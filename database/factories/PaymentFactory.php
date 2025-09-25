<?php

namespace Database\Factories;

use App\Enums\PaymentStatusEnum;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'user_id' => null,
            'invoice_id' => (string) $this->faker->numberBetween(100000, 999999),
            'invoice_key' => $this->faker->uuid(),
            'gateway' => 'instapay',
            'coupon_id' => null,
            'discount' => null,
            'type' => null,
            'amount_before_coupon' => 1000,
            'amount_after_coupon' => 900,
            'amount_confirmed' => 900,
            'payment_method' => 'manual',
            'payment_method_id' => 0,
            'status' => PaymentStatusEnum::Pending,
            'response_payload' => null,
            'payment_data' => null,
            'paid_at' => null,
        ];
    }
}

