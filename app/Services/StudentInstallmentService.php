<?php
// app/Services/CategoryService.php

namespace App\Services;

use App\Models\StudentInstallment;
use Illuminate\Database\Eloquent\Collection;

class StudentInstallmentService
{
    private $courseInstallmentService;
    public function __construct(CourseInstallmentService $courseInstallmentService)
    {
        $this->courseInstallmentService = $courseInstallmentService;
    }

    public function getStudentInstallments(): Collection
    {
        return StudentInstallment::all();
    }

    public function createStudentInstallment(array $data): StudentInstallment
    {
        return StudentInstallment::create($data);
    }

    public function updateStudentInstallment(StudentInstallment $installment, array $data): bool
    {
        $installment->update($data);

        return $installment->wasChanged();
    }

    public function deleteStudentInstallment(StudentInstallment $installment): bool
    {
        return $installment->delete();
    }

    // get student stallment by student_id and course_installment_id
    public function getStudentInstallmentByStudentIdAndCourseInstallmentId(int $studentId, int $courseInstallmentId, $amount = null, $created_at = null): ?StudentInstallment
    {
        $query = StudentInstallment::where('student_id', $studentId)
            ->where('course_installment_id', $courseInstallmentId);

        if ($amount !== null) {
            $query->where('amount', $amount);
        }

        if ($created_at !== null) {
            $query->whereBetween('created_at', [
                $created_at->subSeconds(5),
                $created_at->addSeconds(5)
            ]);
        }

        return $query->orderBy('id', 'desc')->first();
    }

    // get student installments count
    public function getNumberOfInstallmentsPaid(int $studentId, int $courseInstallmentId): int
    {
        return StudentInstallment::where('student_id', $studentId)
            ->where('course_installment_id', $courseInstallmentId)
            ->count();
    }

    public function getStudentInstallment(int $id): StudentInstallment
    {
        return StudentInstallment::findOrFail($id);
    }

    // check if user purchased the course
    public function checkUserAndCourse(int $studentId, int $courseInstallmentId): bool
    {

        $courseInstallment = $this->courseInstallmentService->getCourseInstallment($courseInstallmentId);

        if (!$courseInstallment) {
            throw new \Exception('Course installment not found');
        }

        $num_installments_paid = $this->getNumberOfInstallmentsPaid($studentId, $courseInstallmentId);

        if ($num_installments_paid < $courseInstallment->number_of_installments) {
            return false;
        }

        return true;
    }

    // get next installment price for student
    public function getNextInstallmentPrice(int $studentId, int $courseInstallmentId): ?float
    {
        $courseInstallment = $this->courseInstallmentService->getCourseInstallment($courseInstallmentId);

        if (!$courseInstallment) {
            throw new \Exception('Course installment not found');
        }

        if ($courseInstallment->number_of_installments == 1) {
            return $courseInstallment->course->total_price;
        }

        $num_installments_paid = $this->getNumberOfInstallmentsPaid($studentId, $courseInstallmentId);

        if ($num_installments_paid == 0) {
            return $courseInstallment->installment_amounts[0];
        }

        if ($num_installments_paid < $courseInstallment->number_of_installments) {
            return $courseInstallment->installment_amounts[$num_installments_paid - 1];
        }

        return null;
    }
}
