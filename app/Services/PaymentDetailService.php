<?php

namespace App\Services;

use App\Enums\PaymentType;
use App\Enums\PaymentStatusEnum;
use App\Events\PaymentApprovedEvent;
use App\Models\Cart;
use App\Models\Payment;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use App\Services\PaymentStrategy\CashPayment;
use App\Services\PaymentStrategy\InstallmentPayment;
use App\Services\PaymentStrategy\PaymentContext;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class PaymentDetailService
{

    public function __construct(
        protected PaymentContext $paymentContext,
        protected StudentInstallmentService $studentInstallmentService,
        protected CouponService $couponService,
        protected PaymentRepositoryInterface $paymentRepository,
        protected CartService $cartService,
        protected UserCoursesService $userCoursesService
    ) {}

    // public function store(array $data): Payment
    // {

    //     if (isset($data['coupon_code'])) {
    //         $coupon = $this->couponService->findByCode($data['coupon_code']);
    //         if ($coupon) {
    //             $data['coupon_id'] = $coupon->id;
    //             $data['discount'] = $coupon->discount;
    //             $data['type'] = $coupon->type;
    //             $check = $this->couponService->checkCoupon($data['coupon_code']);
    //             $data['total_before_coupon'] = $check['total_before_coupon'];
    //             $data['total_after_coupon'] = $check['total'];
    //         } else {
    //             throw ValidationException::withMessages(['coupon_code' => 'Invalid coupon code.']);
    //         }
    //     } else {
    //         $data['total_before_coupon'] =
    //             $data['total_after_coupon'] = $check['total'];
    //     }

    //     $payment = Payment::create($data);

    //     foreach ($data['course_installment_ids'] as $item) {
    //         $payment->paymentItems()->create([
    //             'course_installment_id' => $item,
    //         ]);
    //     }



    //     return $payment;
    // }

    public function getPayments()
    {
        $query = Payment::with(['user']);

        // Apply filters based on request parameters
        if (request('user_id')) {
            $query->where('user_id', request('user_id'));
        }

        if (request('gateway')) {
            $query->where('gateway', request('gateway'));
        }

        if (request('status')) {
            $query->where('status', request('status'));
        }

        if (request('from_paid_at')) {
            $query->whereDate('paid_at', '>=', request('from_paid_at'));
        }

        if (request('to_paid_at')) {
            $query->whereDate('paid_at', '<=', request('to_paid_at'));
        }

        return $query;
    }

    public function getPayment($id, $columns = ['*'], $with = []): Payment
    {
        return $this->paymentRepository->find($id, $columns, $with);
    }

    public function getWeeklyPaidCashPayments(string $startOfWeek, string $endOfWeek): Collection
    {
        return $this->paymentRepository->getWeeklyPaidCashPayments($startOfWeek, $endOfWeek);
    }

    public function getWeeklyPaidInstallmetPayments(string $startOfWeek, string $endOfWeek): Collection
    {
        return $this->paymentRepository->getWeeklyPaidInstallmetPayments($startOfWeek, $endOfWeek);
    }

    public function getWeeklySummary(Carbon $startOfWeek, Carbon $endOfWeek): array
    {
        $summaryData = [];

        for ($day = $startOfWeek->copy(); $day->lt($endOfWeek); $day->addDay()) {
            $date = $day->toDateString();

            $summaryData[] = [
                'day' => $day->format('l'),
                'date' => $date,

                'total_subscribers' => (string) $this->paymentRepository->getDailyPaidSubscriberCount($date),
                'total_income' => (string) $this->paymentRepository->getDailyPaidIncome($date),

                'cash_subscribers' => (string) $this->paymentRepository->getDailySubscriberCountByType($date, PaymentType::CASH->value),
                'cash_income' => (string) $this->paymentRepository->getDailyIncomeByType($date, PaymentType::CASH->value),

                'installment_subscribers' => (string) $this->paymentRepository->getDailySubscriberCountByType($date, PaymentType::INSTALLMENT->value),
                'installment_income' => (string) $this->paymentRepository->getDailyIncomeByType($date, PaymentType::INSTALLMENT->value),

                // 'super_graphic_subscribers' => (string) (Payment::whereHas('paymentItems.courseInstallment.course', function ($query) {
                //     $query->where('title', 'LIKE', '%سوبر جرافيك%');
                // })->whereDate('paid_at', $date)->where('status', 'paid')->count() ?: 0),
                // 'mini_graphic_subscribers' => (string) (Payment::whereHas('paymentItems.courseInstallment.course', function ($query) {
                //     $query->where('title', 'LIKE', '%ميني جرافيك%');
                // })->whereDate('paid_at', $date)->where('status', 'paid')->count() ?: 0),
            ];
        }

        // Weekly total row
        $summaryData[] = [
            'day' => 'إجمالي الأسبوع',
            'date' => '',
            'total_subscribers' => (string) array_sum(array_column($summaryData, 'total_subscribers')),
            'total_income' => (string) array_sum(array_column($summaryData, 'total_income')),
            'cash_subscribers' => (string) array_sum(array_column($summaryData, 'cash_subscribers')),
            'cash_income' => (string) array_sum(array_column($summaryData, 'cash_income')),
            'installment_subscribers' => (string) array_sum(array_column($summaryData, 'installment_subscribers')),
            'installment_income' => (string) array_sum(array_column($summaryData, 'installment_income')),
            // 'super_graphic_subscribers' => (string) (array_sum(array_column($summaryData, 'super_graphic_subscribers')) ?: 0),
            // 'mini_graphic_subscribers' => (string) (array_sum(array_column($summaryData, 'mini_graphic_subscribers')) ?: 0),
        ];

        return $summaryData;
    }

    public function updateAmountConfirmed($amount, $id): Payment
    {
        $payment = $this->getPayment($id);

        $payment->amount_confirmed = $amount;

        $payment->save();
        return $payment;
    }

    public function updatePaidAt($paidAt, $id): Payment
    {
        $payment = $this->getPayment($id);

        $payment->paid_at = $paidAt;

        $payment->save();
        return $payment;
    }

    public function updateCoupon($couponId, $id): Payment
    {
        $payment = $this->getPayment($id);

        // Get the old coupon for usage count management
        $oldCoupon = $payment->coupon;

        if ($couponId) {
            // Validate the new coupon exists and is active
            $newCoupon = $this->couponService->find($couponId);

            if (!$newCoupon || !$newCoupon->isValid()) {
                throw new \Exception('Invalid or inactive coupon selected.');
            }

            // Calculate new amounts with the coupon
            $couponData = $this->couponService->apply($newCoupon->code, $payment->amount_before_coupon);

            $payment->coupon_id = $newCoupon->id;
            $payment->discount = $newCoupon->discount;
            $payment->type = $newCoupon->type;
            $payment->amount_after_coupon = $couponData['total'];
            $payment->amount_confirmed = $couponData['total'];
        } else {
            // Remove coupon
            $payment->coupon_id = null;
            $payment->discount = null;
            $payment->type = null;
            $payment->amount_after_coupon = $payment->amount_before_coupon;
            $payment->amount_confirmed = $payment->amount_before_coupon;
        }

        $payment->save();

        // Update usage counts
        if ($oldCoupon && $oldCoupon->usage_count > 0) {
            $oldCoupon->decrement('usage_count');
        }

        if ($couponId && isset($newCoupon)) {
            $newCoupon->increment('usage_count');
        }

        return $payment;
    }

    public function changeStatus($status, $id)
    {
        $payment = $this->getPayment($id);
        if ($status == PaymentStatusEnum::Paid->value) {
            $payment->paid_at = now();
            $this->handleApproval($payment);
        } elseif ($status == PaymentStatusEnum::Cancelled->value) {
            // $this->handleRejection($payment);
        }

        $payment->status = $status;
        $payment->save();

        return $payment;
    }

    private function handleApproval(Payment $payment): void
    {

        // Handle different payment gateways
        if ($payment->gateway === 'instapay') {
            $this->validateInstapayApproval($payment);
            $this->handleInstapayApproval($payment);
        } else {
            // Original logic for other payment types
            $payment->load('coupon', 'paymentItems');

            foreach ($payment->paymentItems as $item) {
                $this->setPaymentStrategy($item->payment_type);

                $expiresAt = $this->applyCouponAccessForItem($payment, $item);

                $this->paymentContext->handlePayment($item, $payment->user_id, $expiresAt);

            }
        }
    }

    private function handleInstapayApproval(Payment $payment): void
    {
        if (!$payment) {
            return;
        }

        $payment->load('paymentItems', 'coupon');

        foreach ($payment->paymentItems as $item) {
            if ($item->course_id) {
                $item->load(['course', 'courseInstallment', 'packagePlan']);
                $this->setPaymentStrategy($item->payment_type);

                $expiresAt = $this->applyCouponAccessForItem($payment, $item);

                $this->paymentContext->handlePayment($item, $payment->user_id, $expiresAt);
            }
        }
        try {
            event(new PaymentApprovedEvent([$payment->user_id], [
                'payment' => $payment
            ]));
        } catch (\Exception $e) {
            Log::error("Error in PaymentApprovedEvent");
            Log::error($e->getMessage());
        }

        $coupon = $payment->coupon;
        if ($coupon) {
            $coupon->update([
                'usage_count' => $coupon->usage_count + 1
            ]);
        }

        // empty cart for user
        Cart::forUser($payment->user_id)->delete();
    }

    private function handleRejection(Payment $payment): void
    {
        $this->validateRejection($payment);

        foreach ($payment->paymentItems as $item) {
            $this->setPaymentStrategy($item->payment_type);
            $this->paymentContext->handleRejectPayment($item, $payment->user_id);
        }
    }

    private function validateInstapayApproval(Payment $payment): void
    {
        if ($payment->amount_confirmed == 0 || !$payment->amount_confirmed) {
            throw ValidationException::withMessages(['amount' => 'Please enter an amount first.']);
        }
    }

    private function validateRejection(Payment $payment): void
    {
        $today = now();
        $approvedAt = $payment->approved_at;
        if (!$approvedAt) {
            return;
        }
        $nextFriday = $approvedAt->copy()->next(Carbon::FRIDAY);
        $payment_status = $payment->status;

        if (
            $payment_status == PaymentStatusEnum::Paid->value &&  $nextFriday->between($approvedAt, $today)
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

    // Apply coupon-based access window per course item
    private function applyCouponAccessForItem(Payment $payment, \App\Models\PaymentItems $item): Carbon|null
    {
        $coupon = $payment->coupon;
        if (!$item->course_id) {
            return null;
        }

        $expiresAt = null;
        if ($coupon && !empty($coupon->access_duration_days) && $coupon->access_duration_days > 0) {
            $expiresAt = now()->addDays((int)$coupon->access_duration_days);
        }
        return $expiresAt;
        // $this->userCoursesService->setCourseExpiry($payment->user_id, $item->course_id, $expiresAt);
    }
}
