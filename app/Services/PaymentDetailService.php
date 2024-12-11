<?php

namespace App\Services;

use App\Enums\Status;
use App\Models\PaymentDetails;

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

    public function changeStatus($status, $id)
    {
        $paymentDetail = PaymentDetails::find($id);
        $paymentDetail->status = $status;

        if ($status == Status::APPROVED) {
            $paymentDetail->approved_by = auth()->user()->id;
            $paymentDetail->approved_at = now();
        } elseif ($status == Status::REJECTED) {
            $paymentDetail->rejected_by = auth()->user()->id;
            $paymentDetail->rejected_at = now();
        }

        $paymentDetail->save();

        return $paymentDetail;
    }
}
