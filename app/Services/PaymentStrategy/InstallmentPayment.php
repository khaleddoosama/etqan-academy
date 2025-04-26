<?php

namespace App\Services\PaymentStrategy;

use App\Models\PaymentItems;
use App\Services\StudentInstallmentService;
use App\Services\UserCoursesService;
use App\Services\UserService;
use Carbon\Carbon;
use Exception;

class InstallmentPayment implements PaymentStrategyInterface
{
    protected StudentInstallmentService $studentInstallmentService;
    private UserService $userService;
    private UserCoursesService $userCoursesService;

    public function __construct(StudentInstallmentService $studentInstallmentService, UserService $userService, UserCoursesService $userCoursesService)
    {
        $this->studentInstallmentService = $studentInstallmentService;
        $this->userService = $userService;
        $this->userCoursesService = $userCoursesService;
    }

    /**
     * @throws Exception
     */
    public function handlePayment(PaymentItems $paymentItem, $user_id): bool
    {
        $course_installment = $paymentItem->courseInstallment;
        $this->userCoursesService->createUserCourse($user_id, $course_installment->course_id);


        $number_of_installments = $course_installment->number_of_installments;

        $num_installments_paid = $this->studentInstallmentService->getNumberOfInstallmentsPaid($user_id, $course_installment->id) + 1;

        $due_date = null;
        if ($num_installments_paid < $number_of_installments) {
            $due_date = $this->calculateNextDueDate($user_id, $course_installment, $num_installments_paid);
        } elseif ($num_installments_paid > $number_of_installments) {
            throw new Exception('Installments already paid');
        }

        $amount = $paymentItem->amount;
        $remaining_amount = $paymentItem->amount - $course_installment->installment_amounts[$num_installments_paid - 1];

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

    public function handleRejectPayment(PaymentItems $paymentItem, $user_id): bool
    {
        $student = $this->userService->getUser($user_id);
        $this->userCoursesService->changeUserCourseStatus(['status' => 0], $student, $paymentItem->courseInstallment->course);


        $installment = $this->studentInstallmentService->getStudentInstallmentByStudentIdAndCourseInstallmentId(
            $user_id,
            $paymentItem->courseInstallment->id,
            $paymentItem->amount,
            $paymentItem->approved_at
        );

        if ($installment) {
            $this->studentInstallmentService->deleteStudentInstallment($installment);
        }


        return true;
    }
}
