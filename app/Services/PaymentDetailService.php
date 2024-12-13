<?php

namespace App\Services;

use App\Enums\Status;
use App\Models\PaymentDetails;
use Illuminate\Validation\ValidationException;

class PaymentDetailService
{
    protected $courseService;
    protected $userService;
    // constructor for CourseService
    public function __construct(CourseService $courseService, UserService $userService)
    {
        $this->courseService = $courseService;
        $this->userService = $userService;
    }

    public function store(array $data): PaymentDetails
    {
        $course = $this->courseService->getCourseBySlug($data['course_slug']);
        $data['course_id'] = $course->id;
        unset($data['course_slug']);

        return PaymentDetails::create($data);
    }

    public function getPaymentDetails()
    {
        return PaymentDetails::all();
    }

    public function getPaymentDetail($id): PaymentDetails
    {
        return PaymentDetails::find($id);
    }

    public function update(array $data, $id): PaymentDetails
    {
        $paymentDetail = $this->getPaymentDetail($id);
        $paymentDetail->update($data);
        return $paymentDetail;
    }

    public function changeStatus($status, $id)
    {
        $paymentDetail = $this->getPaymentDetail($id);
        $paymentDetail->status = $status;
        if ($status == Status::APPROVED->value) {
            // if amount == 0 then throw error
            if ($paymentDetail->amount == 0) {
                throw ValidationException::withMessages(['amount' => 'please enter amount first']);
            }
            $paymentDetail->approved_by = auth()->user()->id;
            $paymentDetail->approved_at = now();
        } elseif ($status == Status::REJECTED->value) {
            $paymentDetail->rejected_by = auth()->user()->id;
            $paymentDetail->rejected_at = now();
        }

        $paymentDetail->save();

        return $paymentDetail;
    }
}
