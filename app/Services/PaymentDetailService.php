<?php

namespace App\Services;

use App\Models\PaymentDetail;
use App\Models\PaymentDetails;

class PaymentDetailService
{
    public function store(array $data): PaymentDetails
    {
        return PaymentDetails::create($data);
    }
}
