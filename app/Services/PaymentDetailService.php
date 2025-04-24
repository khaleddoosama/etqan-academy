<?php

namespace App\Services;

use App\Enums\PaymentType;
use App\Enums\Status;
use App\Models\Payment;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use App\Services\PaymentStrategy\CashPayment;
use App\Services\PaymentStrategy\InstallmentPayment;
use App\Services\PaymentStrategy\PaymentContext;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class PaymentDetailService
{

    public function __construct(
        protected PaymentContext $paymentContext,
        protected StudentInstallmentService $studentInstallmentService,
        protected CouponService $couponService,
        protected PaymentRepositoryInterface $paymentRepository
    ) {}

    public function store(array $data): Payment
    {

        if (isset($data['coupon_code'])) {
            $coupon = $this->couponService->findByCode($data['coupon_code']);
            if ($coupon) {
                $data['coupon_id'] = $coupon->id;
                $data['discount'] = $coupon->discount;
                $data['type'] = $coupon->type;
                $check = $this->couponService->checkCoupon($data['coupon_code']);
                $data['total_before_coupon'] = $check['total_before_coupon'];
                $data['total_after_coupon'] = $check['total'];
            } else {
                throw ValidationException::withMessages(['coupon_code' => 'Invalid coupon code.']);
            }
        } else {
            $data['total_before_coupon'] =
                $data['total_after_coupon'] = $check['total'];
        }

        $payment = Payment::create($data);

        foreach ($data['course_installment_ids'] as $item) {
            $payment->paymentItems()->create([
                'course_installment_id' => $item,
            ]);
        }



        return $payment;
    }

    public function getPayments()
    {
        return $this->paymentRepository->filterByRequest(request(), ['*'], ['user']);
    }

    public function getPayment($id): Payment
    {
        return Payment::findOrFail($id);
    }

    public function updateAmount($amount, $id): Payment
    {
        $payment = $this->getPayment($id);
        $payment->amount = $amount;
        $payment->save();
        return $payment;
    }

    public function changeStatus($status, $id)
    {
        $payment = $this->getPayment($id);
        if ($status == Status::APPROVED->value) {
            $this->handleApproval($payment);
        } elseif ($status == Status::REJECTED->value) {
            $this->handleRejection($payment);
        }

        $payment->status = $status;
        $payment->save();

        return $payment;
    }

    private function handleApproval(Payment $payment): void
    {
        $this->validateApproval($payment);


        $this->setPaymentStrategy($payment->payment_type);
        $this->paymentContext->handlePayment($payment, $payment->user_id);
    }

    private function handleRejection(Payment $payment): void
    {
        $this->validateRejection($payment);


        $this->setPaymentStrategy($payment->payment_type);
        $this->paymentContext->handleRejectPayment($payment, $payment->user_id);
    }

    private function validateApproval(Payment $payment): void
    {
        if ($payment->amount == 0) {
            throw ValidationException::withMessages(['amount' => 'Please enter an amount first.']);
        }
    }

    private function validateRejection(Payment $payment): void
    {
        $today = now();
        $approvedAt = $payment->approved_at;
        $nextFriday = $approvedAt->copy()->next(Carbon::FRIDAY);
        $payment_status = $payment->status;

        if (
            $payment_status == Status::APPROVED->value &&  $nextFriday->between($approvedAt, $today)
        ) {
            throw ValidationException::withMessages(['status' => 'You cannot reject a payment approved in a previous week after Friday']);
        }
    }

    private function setPaymentStrategy(PaymentType $paymentType): void
    {
        $strategy = match ($paymentType) {
            PaymentType::CASH => new CashPayment(new UserService(), new UserCoursesService()),
            PaymentType::INSTALLMENT => new InstallmentPayment($this->studentInstallmentService, new UserService(), new UserCoursesService()),
            default => throw new \InvalidArgumentException('Invalid payment type.'),
        };

        $this->paymentContext->setPaymentStrategy($strategy);
    }

}
