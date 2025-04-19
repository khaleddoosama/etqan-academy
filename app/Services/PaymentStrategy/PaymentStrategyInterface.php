<?php

namespace App\Services\PaymentStrategy;

use App\Models\Payment;
use App\Services\UserCoursesService;

interface PaymentStrategyInterface
{
    public function handlePayment(Payment $paymentDetail, $user_id): bool;

    public function handleRejectPayment(Payment $paymentDetail, $user_id): bool;
}
