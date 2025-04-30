<?php

namespace App\Services\PaymentStrategy;

use App\Models\Payment;
use App\Models\PaymentItems;

class PaymentContext
{
    private PaymentStrategyInterface $strategy;

    public function setPaymentStrategy(PaymentStrategyInterface $strategy): void
    {
        $this->strategy = $strategy;
    }

    public function handlePayment(PaymentItems $paymentItem, $user_id): bool
    {
        return $this->strategy->handlePayment($paymentItem, $user_id);
    }

    public function handleRejectPayment(PaymentItems $paymentItem, $user_id): bool
    {
        return $this->strategy->handleRejectPayment($paymentItem, $user_id);
    }
}
