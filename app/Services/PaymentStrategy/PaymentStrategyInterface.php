<?php

namespace App\Services\PaymentStrategy;

use App\Models\PaymentDetails;
use App\Services\UserCoursesService;

interface PaymentStrategyInterface
{
    public function handlePayment(PaymentDetails $paymentDetail, $user_id): bool;

    // handle reject payment
    public function handleRejectPayment(PaymentDetails $paymentDetail, $user_id): bool;
}
