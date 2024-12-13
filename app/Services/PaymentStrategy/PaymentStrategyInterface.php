<?php

namespace App\Services\PaymentStrategy;

use App\Models\PaymentDetails;
use App\Services\UserCoursesService;

interface PaymentStrategyInterface
{
    public function handlePayment(UserCoursesService $userCoursesService, PaymentDetails $paymentDetail, $user_id): bool;
}
