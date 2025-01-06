<?php

namespace App\Services\PaymentStrategy;

use App\Models\PaymentDetails;
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

    public function handlePayment(PaymentDetails $paymentDetail, $user_id): bool
    {
        $this->userCoursesService->createUserCourse($user_id, $paymentDetail->courseInstallment->course_id);
        return true;
    }

    public function handleRejectPayment(PaymentDetails $paymentDetail, $user_id): bool
    {
        $student = $this->userService->getUser($user_id);
        $this->userCoursesService->changeUserCourseStatus(['status' => 0], $student, $paymentDetail->courseInstallment->course);
        return true;
    }
}
