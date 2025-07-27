<?php

namespace App\Repositories\Eloquent;

use App\Enums\PaymentType;
use App\Enums\Status;
use App\Models\Payment;
use App\Models\PaymentItems;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use Illuminate\Support\Collection;

class PaymentRepository extends BaseRepository implements PaymentRepositoryInterface
{
    protected function model(): Payment
    {
        return new Payment();
    }


    protected function filterable(): array
    {
        return [
            'user_id'   => 'exact',
            'status'    => 'like',
            'created_at' => 'date',
        ];
    }

    public function getWeeklyPaidCashPayments(string $startOfWeek, string $endOfWeek): Collection
    {
        return $this->model
            ->with([
                'user',
                'paymentItems' => function ($query) {
                    $query->where('payment_type', PaymentType::CASH->value)
                        ->with(['packagePlan', 'course']);
                },
            ])
            ->whereBetween('paid_at', [$startOfWeek, $endOfWeek])
            ->where('status', 'paid')
            ->get();
    }

    public function getWeeklyPaidInstallmetPayments(string $startOfWeek, string $endOfWeek): Collection
    {
        return $this->model
            ->with([
                'user',
                'paymentItems' => function ($query) {
                    $query->where('payment_type', PaymentType::INSTALLMENT->value)
                        ->with(['courseInstallment', 'packagePlan', 'course']);
                },
            ])
            ->whereBetween('paid_at', [$startOfWeek, $endOfWeek])
            ->where('status', 'paid')
            ->get();
    }

    public function getDailyPaidSubscriberCount(string $date): int
    {
        return $this->model
            ->whereDate('paid_at', $date)
            ->where('status', 'paid')
            ->count();
    }

    public function getDailyPaidIncome(string $date): float
    {
        return $this->model
            ->whereDate('paid_at', $date)
            ->where('status', 'paid')
            ->sum('amount_after_coupon');
    }

    public function getDailySubscriberCountByType(string $date, string $type): int
    {
        return PaymentItems::where('payment_type', $type)
            ->whereHas('payment', function ($q) use ($date) {
                $q->whereDate('paid_at', $date)->where('status', 'paid');
            })
            ->distinct('payment_id')
            ->count('payment_id');
    }

    public function getDailyIncomeByType(string $date, string $type): float
    {
        return PaymentItems::where('payment_type', $type)
            ->whereHas('payment', function ($q) use ($date) {
                $q->whereDate('paid_at', $date)->where('status', 'paid');
            })
            ->sum('amount');
    }

    public function canUserPay($userId): bool
    {
        $lastPayment = Payment::where('user_id', $userId)
            ->where('gateway', 'instapay')
            ->orderBy('created_at', 'desc')
            ->first();

        return !$lastPayment || $lastPayment->created_at->diffInMinutes(now()) >= 5;
    }
}
