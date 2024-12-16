<?php

namespace App\Services;

use App\Enums\PaymentType;
use App\Enums\Status;
use App\Models\PaymentDetails;
use App\Services\PaymentStrategy\CashPayment;
use App\Services\PaymentStrategy\InstallmentPayment;
use App\Services\PaymentStrategy\PaymentContext;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class PaymentDetailService
{
    protected $paymentContext;
    protected $studentInstallmentService;
    public function __construct(PaymentContext $paymentContext, StudentInstallmentService $studentInstallmentService)
    {
        $this->paymentContext = $paymentContext;
        $this->studentInstallmentService = $studentInstallmentService;
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
        return PaymentDetails::findOrFail($id);
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
        if ($status == Status::APPROVED->value) {
            $this->handleApproval($paymentDetail);
        } elseif ($status == Status::REJECTED->value) {
            $this->handleRejection($paymentDetail);
        }

        $paymentDetail->status = $status;
        $paymentDetail->save();

        return $paymentDetail;
    }

    private function handleApproval(PaymentDetails $paymentDetail): void
    {
        $this->validateApproval($paymentDetail);

        $paymentDetail->approved_by = auth()->user()->id;
        $paymentDetail->approved_at = now();

        $this->setPaymentStrategy($paymentDetail->payment_type);
        $this->paymentContext->handlePayment($paymentDetail, $paymentDetail->user_id);
    }

    private function handleRejection(PaymentDetails $paymentDetail): void
    {
        $this->validateRejection($paymentDetail);

        $paymentDetail->rejected_by = auth()->user()->id;
        $paymentDetail->rejected_at = now();

        $this->setPaymentStrategy($paymentDetail->payment_type);
        $this->paymentContext->handleRejectPayment($paymentDetail, $paymentDetail->user_id);
    }

    private function validateApproval(PaymentDetails $paymentDetail): void
    {
        if ($paymentDetail->amount === 0) {
            throw ValidationException::withMessages(['amount' => 'Please enter an amount first.']);
        }
    }

    private function validateRejection(PaymentDetails $paymentDetail): void
    {
        $today = now();
        $approvedAt = $paymentDetail->approved_at;
        $nextFriday = $approvedAt->copy()->next(Carbon::FRIDAY);
        $payment_status = $paymentDetail->status;

        if (
            $payment_status == Status::APPROVED->value &&  $nextFriday->between($approvedAt, $today)
        ) {
            throw ValidationException::withMessages(['status' => 'You cannot reject a payment approved in a previous week after Friday']);
        }
    }

    private function setPaymentStrategy(PaymentType $paymentType): void
    {
        $strategy = match ($paymentType) {
            PaymentType::CASH => new CashPayment(),
            PaymentType::INSTALLMENT => new InstallmentPayment($this->studentInstallmentService),
            default => throw new \InvalidArgumentException('Invalid payment type.'),
        };

        $this->paymentContext->setPaymentStrategy($strategy);
    }
}
