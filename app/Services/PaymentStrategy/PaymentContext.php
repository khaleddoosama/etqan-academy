<?php

namespace App\Services\PaymentStrategy;

use App\Models\CourseInstallment;
use App\Models\PaymentDetails;
use App\Services\CourseService;
use App\Services\UserCoursesService;
use App\Services\UserService;

class PaymentContext
{
    private PaymentStrategyInterface $strategy;

    public function setPaymentStrategy(PaymentStrategyInterface $strategy): void
    {
        $this->strategy = $strategy;
    }

    public function handlePayment(PaymentDetails $paymentDetail, $user_id, $amount = null): bool
    {
        $userCoursesService = new UserCoursesService(new CourseService(), new UserService());

        return $this->strategy->handlePayment($userCoursesService, $paymentDetail, $user_id, $amount);
    }
}
