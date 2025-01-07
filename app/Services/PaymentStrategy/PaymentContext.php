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
    private UserCoursesService $userCoursesService;

    public function __construct(UserCoursesService $userCoursesService)
    {
        $this->userCoursesService = $userCoursesService;
    }
    public function setPaymentStrategy(PaymentStrategyInterface $strategy): void
    {
        $this->strategy = $strategy;
    }

    public function handlePayment(PaymentDetails $paymentDetail, $user_id): bool
    {
        // $userCoursesService = new UserCoursesService(new CourseService(), new UserService());
        return $this->strategy->handlePayment($paymentDetail, $user_id);
    }

    public function handleRejectPayment(PaymentDetails $paymentDetail, $user_id): bool
    {
        // $userCoursesService = new UserCoursesService(new CourseService(), new UserService());
        return $this->strategy->handleRejectPayment($paymentDetail, $user_id);
    }
}
