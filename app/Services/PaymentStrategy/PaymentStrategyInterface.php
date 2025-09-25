<?php

namespace App\Services\PaymentStrategy;

use App\Models\PaymentItems;
use Carbon\Carbon;

interface PaymentStrategyInterface
{
    public function handlePayment(PaymentItems $paymentItem, $user_id, Carbon|null $expiresAt): bool;

    public function handleRejectPayment(PaymentItems $paymentItem, $user_id): bool;
}
