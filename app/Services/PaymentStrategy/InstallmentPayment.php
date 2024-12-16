<?php

namespace App\Services\PaymentStrategy;

use App\Models\PaymentDetails;
use App\Models\StudentInstallment;
use App\Services\StudentInstallmentService;
use App\Services\UserCoursesService;
use Carbon\Carbon;

class InstallmentPayment implements PaymentStrategyInterface
{
    protected StudentInstallmentService $studentInstallmentService;

    public function __construct(StudentInstallmentService $studentInstallmentService)
    {
        $this->studentInstallmentService = $studentInstallmentService;
    }
    public function handlePayment(UserCoursesService $userCoursesService, PaymentDetails $paymentDetail, $user_id): bool
    {
        $course_installment = $paymentDetail->courseInstallment;
        $userCoursesService->createUserCourse($user_id, $course_installment->course_id);


        $number_of_installments = $course_installment->number_of_installments;

        $num_installments_paid = $this->studentInstallmentService->getNumberOfInstallmentsPaid($user_id, $course_installment->id) + 1;

        $due_date = null;
        if ($num_installments_paid < $number_of_installments) {
            $due_date = $this->calculateNextDueDate($user_id, $course_installment, $num_installments_paid);
        } elseif ($num_installments_paid > $number_of_installments) {
            throw new \Exception('Installments already paid');
        }

        $amount = $paymentDetail->amount;
        $remaining_amount = $paymentDetail->amount - $course_installment->installment_amounts[$num_installments_paid - 1];

        $this->createStudentInstallment($user_id, $course_installment->id, $amount, $remaining_amount, $due_date);

        return true;
    }

    private function calculateNextDueDate(int $userId, $courseInstallment, int $numInstallmentsPaid): ?Carbon
    {
        $lastInstallment = $this->studentInstallmentService->getStudentInstallmentByStudentIdAndCourseInstallmentId($userId, $courseInstallment->id);

        if ($lastInstallment && $lastInstallment->due_date && !Carbon::parse($lastInstallment->due_date)->isPast()) {
            return Carbon::parse($lastInstallment->due_date)->addDays($courseInstallment->installment_duration);
        }

        return now()->addDays($courseInstallment->installment_duration);
    }

    private function calculateRemainingAmount(float $amountPaid, array $installmentAmounts, int $currentInstallmentIndex): float
    {
        return $amountPaid - $installmentAmounts[$currentInstallmentIndex - 1];
    }

    private function createStudentInstallment(int $userId, int $courseInstallmentId, float $amount, float $remainingAmount, ?Carbon $dueDate): void
    {
        $data = [
            'student_id' => $userId,
            'course_installment_id' => $courseInstallmentId,
            'amount' => $amount,
            'remaining_amount' => $remainingAmount,
            'due_date' => $dueDate,
        ];

        $this->studentInstallmentService->createStudentInstallment($data);
    }

    public function handleRejectPayment(UserCoursesService $userCoursesService, PaymentDetails $paymentDetail, $user_id): bool
    {
        $student = $userCoursesService->getStudent($user_id);
        $userCoursesService->changeUserCourseStatus(['status' => 0], $student, $paymentDetail->courseInstallment->course);


        $installment = $this->studentInstallmentService->getStudentInstallmentByStudentIdAndCourseInstallmentId(
            $user_id,
            $paymentDetail->courseInstallment->id,
            $paymentDetail->amount,
            $paymentDetail->approved_at
        );

        if ($installment) {
            $this->studentInstallmentService->deleteStudentInstallment($installment);
        }


        return true;
    }
}
