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
}
