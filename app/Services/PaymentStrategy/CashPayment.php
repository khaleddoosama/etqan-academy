<?php

namespace App\Services\PaymentStrategy;

use App\Models\PaymentItems;
use App\Services\UserCoursesService;
use App\Services\UserService;
use Carbon\Carbon;

class CashPayment implements PaymentStrategyInterface
{
    private UserService $userService;
    private UserCoursesService $userCoursesService;

    public function __construct(UserService $userService, UserCoursesService $userCoursesService)
    {
        $this->userService = $userService;
        $this->userCoursesService = $userCoursesService;
    }

    public function handlePayment(PaymentItems $paymentItem, $user_id, Carbon|null $expiresAt): bool
    {
        $this->userCoursesService->createUserCourse($user_id, $paymentItem->course_id, $expiresAt);
        return true;
    }

    public function handleRejectPayment(PaymentItems $paymentItem, $user_id): bool
    {
        $student = $this->userService->getUser($user_id);

        $this->userCoursesService->changeUserCourseStatus(
            ['status' => 0],
            $student,
            $paymentItem->course
        );
        return true;
    }
}
