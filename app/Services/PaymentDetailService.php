<?php

namespace App\Services;

use App\Enums\PaymentType;
use App\Enums\Status;
use App\Models\PaymentDetails;
use App\Services\PaymentStrategy\CashPayment;
use App\Services\PaymentStrategy\InstallmentPayment;
use App\Services\PaymentStrategy\PaymentContext;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class PaymentDetailService
{
    protected $paymentContext;
    public function __construct(PaymentContext $paymentContext)
    {
        $this->paymentContext = $paymentContext;
    }

    public function store(array $data): PaymentDetails
    {
        // $course = $this->courseService->getCourseBySlug($data['course_slug']);
        // $data['course_id'] = $course->id;
        // unset($data['course_slug']);

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

            $type = $paymentDetail->payment_type;
            if ($type === PaymentType::CASH) {
                $this->paymentContext->setPaymentStrategy(new CashPayment());
            } elseif ($type === PaymentType::INSTALLMENT) {
                $this->paymentContext->setPaymentStrategy(new InstallmentPayment());
            } else {
                throw new \Exception('Invalid payment type');
            }

            $this->paymentContext->handlePayment($paymentDetail, $paymentDetail->user_id);
        } elseif ($status == Status::REJECTED->value) {
            $paymentDetail->rejected_by = auth()->user()->id;
            $paymentDetail->rejected_at = now();
        }

        $paymentDetail->save();

        return $paymentDetail;
    }
}
