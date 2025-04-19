<?php

namespace App\Services\PaymentStrategy;

use App\Models\Payment;
use App\Services\UserCoursesService;
use App\Services\UserService;

class CashPayment implements PaymentStrategyInterface
{
    private UserService $userService;
    private UserCoursesService $userCoursesService;

    public function __construct(UserService $userService, UserCoursesService $userCoursesService)
    {
        $this->userService = $userService;
        $this->userCoursesService = $userCoursesService;
    }

    public function handlePayment(Payment $payment, $user_id): bool
    {
        foreach ($payment->paymentItems as $paymentItem) {
            $this->userCoursesService->createUserCourse($user_id, $paymentItem->courseInstallment->course_id);
        }
        return true;
    }

    public function handleRejectPayment(Payment $payment, $user_id): bool
    {
        $student = $this->userService->getUser($user_id);

        foreach ($payment->paymentItems as $paymentItem) {
            $this->userCoursesService->changeUserCourseStatus(
                ['status' => 0],
                $student,
                $paymentItem->courseInstallment->course
            );
        }
        return true;
    }
}
