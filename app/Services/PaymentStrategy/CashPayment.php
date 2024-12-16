<?php

namespace App\Services\PaymentStrategy;

use App\Models\PaymentDetails;
use App\Services\UserCoursesService;

class CashPayment implements PaymentStrategyInterface
{
    public function handlePayment(UserCoursesService $userCoursesService, PaymentDetails $paymentDetail, $user_id): bool
    {
        $userCoursesService->createUserCourse($user_id, $paymentDetail->courseInstallment->course_id);
        return true;
    }

    public function handleRejectPayment(UserCoursesService $userCoursesService, PaymentDetails $paymentDetail, $user_id): bool
    {
        $student = $userCoursesService->getStudent($user_id);
        $userCoursesService->changeUserCourseStatus(['status' => 0], $student, $paymentDetail->courseInstallment->course);
        return true;
    }
}
