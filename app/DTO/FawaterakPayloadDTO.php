<?php

namespace App\DTO;

use Illuminate\Support\Collection;

class FawaterakPayloadDTO
{
    public function __construct(
        public array $inputData,
        public object $user,
        public Collection $carts,
        public float $totalPriceBeforeCoupon,
        public ?array $couponData = null
    ) {}
}
