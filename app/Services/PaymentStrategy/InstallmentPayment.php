<?php

namespace App\Services\PaymentStrategy;

use App\Models\PaymentDetails;
use App\Models\StudentInstallment;
use App\Services\UserCoursesService;
use Carbon\Carbon;

class InstallmentPayment implements PaymentStrategyInterface
{
    public function handlePayment(UserCoursesService $userCoursesService, PaymentDetails $paymentDetail, $user_id): bool
    {
        $course_installment = $paymentDetail->courseInstallment;
        $userCoursesService->createUserCourse($user_id, $course_installment->course_id);


        $number_of_installments = $course_installment->number_of_installments;

        $num_installments_paid = StudentInstallment::where('student_id', $user_id)
            ->where('course_installment_id', $course_installment->id)
            ->count() + 1;

        $due_date = null;
        if ($num_installments_paid < $number_of_installments) {
            // get last student installment due date
            $lastInstallment = StudentInstallment::where('student_id', $user_id)
                ->where('course_installment_id', $course_installment->id)
                ->orderBy('id', 'desc')
                ->first();
            // check if last installment due date is past
            if ($lastInstallment && $lastInstallment->due_date && !Carbon::parse($lastInstallment->due_date)->isPast()) {
                // add installment duration to last installment due date
                $due_date = Carbon::parse($lastInstallment->due_date)->addDays($course_installment->installment_duration);
            } else {
                $due_date = now()->addDays($course_installment->installment_duration);
            }
        } elseif ($num_installments_paid > $number_of_installments) {
            throw new \Exception('Installments already paid');
        }
        $amount = $course_installment->installment_amounts[$num_installments_paid - 1];
        $remaining_amount = $paymentDetail->amount - $amount;

        StudentInstallment::create(
            [
                'student_id' => $user_id,
                'course_installment_id' => $course_installment->id,
                'amount' => $amount,
                'remaining_amount' => $remaining_amount,
                'due_date' => $due_date,
            ]
        );

        return true;
    }
}
