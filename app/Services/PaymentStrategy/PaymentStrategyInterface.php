<?php

namespace App\Services\PaymentStrategy;

use App\Models\PaymentItems;

interface PaymentStrategyInterface
{
    public function handlePayment(PaymentItems $paymentItem, $user_id): bool;

    public function handleRejectPayment(PaymentItems $paymentItem, $user_id): bool;
}
