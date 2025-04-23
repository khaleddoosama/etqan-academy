<?php

namespace App\DTO;

use Illuminate\Support\Collection;

class PaymentRecordDTO
{
    public function __construct(
        public array $responseKeys,
        public object $user,
        public Collection $carts,
        public float $finalPriceAfterCoupon,
        public float $totalPriceBeforeCoupon,
        public ?object $coupon,
        public int $paymentMethodId,
        public ?string $paymentMethod = null
    ) {}
}
