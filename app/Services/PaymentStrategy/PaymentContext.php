<?php

namespace App\Services\PaymentStrategy;

use App\Models\Payment;


class PaymentContext
{
    private PaymentStrategyInterface $strategy;

    public function setPaymentStrategy(PaymentStrategyInterface $strategy): void
    {
        $this->strategy = $strategy;
    }

    public function handlePayment(Payment $payment, $user_id): bool
    {
        return $this->strategy->handlePayment($payment, $user_id);
    }

    public function handleRejectPayment(Payment $payment, $user_id): bool
    {
        return $this->strategy->handleRejectPayment($payment, $user_id);
    }
}
