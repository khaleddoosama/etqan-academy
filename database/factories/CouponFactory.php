<?php

namespace Database\Factories;

use App\Models\Coupon;
use Illuminate\Database\Eloquent\Factories\Factory;

class CouponFactory extends Factory
{
    protected $model = Coupon::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper($this->faker->unique()->bothify('COUPON##??')),
            'discount' => 10,
            'type' => 'percentage',
            'start_at' => now()->startOfDay(),
            'expires_at' => now()->addMonth(),
            'usage_limit' => null,
            'usage_count' => 0,
            'status' => true,
            'access_duration_days' => null,
        ];
    }
}

