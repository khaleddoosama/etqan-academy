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

    public function getTotalPayments(?string $fromDate, ?string $toDate): float
    {
        $query =  $this->model->where('status', 'paid');
        if ($fromDate) {
            $query->whereDate('paid_at', '>=', $fromDate);
        }
        if ($toDate) {
            $query->whereDate('paid_at', '<=', $toDate);
        }
        return $query->sum('amount_confirmed');
    }

    public function getPaidCountByDateRange(?string $fromDate, ?string $toDate): int
    {
        $query =  $this->model->where('status', 'paid');
        if ($fromDate) {
            $query->whereDate('paid_at', '>=', $fromDate);
        }
        if ($toDate) {
            $query->whereDate('paid_at', '<=', $toDate);
        }
        return $query->count();
    }

    public function getPaymentsWithFilters(array $filters): array
    {
        $query = $this->model->with(['user', 'paymentItems']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['gateway'])) {
            $query->where('gateway', $filters['gateway']);
        }

        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (isset($filters['from_date'])) {
            $query->whereDate('paid_at', '>=', $filters['from_date']);
        }

        if (isset($filters['to_date'])) {
            $query->whereDate('paid_at', '<=', $filters['to_date']);
        }

        return $query->orderBy('paid_at', 'desc')
            ->get()
            ->map(function ($payment) {
                $services = $payment->paymentItems->pluck('serviceTitle')->filter()->implode(', ');
                return [
                    'id' => $payment->id,
                    'user_name' => $payment->user->name ?? '-',
                    'user_email' => $payment->user->email ?? '-',
                    'gateway' => $payment->gateway,
                    'services' => $services ?: '-',
                    'amount_confirmed' => $payment->amount_confirmed,
                    'paid_at' => $payment->paid_at,
                    'created_at' => $payment->created_at->format('Y-m-d H:i:s'),
                    'status' => $payment->status
                ];
            })
            ->toArray();
    }

    public function getPaymentMethodsBreakdown(?string $fromDate, ?string $toDate): array
    {

        $query = $this->model->where('status', 'paid');
        if ($fromDate) {
            $query->whereDate('paid_at', '>=', $fromDate);
        }
        if ($toDate) {
            $query->whereDate('paid_at', '<=', $toDate);
        }
        return $query->selectRaw('gateway, SUM(amount_confirmed) as total, COUNT(*) as count')
            ->groupBy('gateway')
            ->get()
            ->map(function ($item) {
                return [
                    'method' => ucfirst($item->gateway ?? 'Unknown'),
                    'total' => $item->total,
                    'count' => $item->count
                ];
            })
            ->toArray();
    }

    public function getPaymentsBreakdown(?string $fromDate, ?string $toDate): array
    {

        $query = $this->model->where('status', 'paid');
        if ($fromDate) {
            $query->whereDate('paid_at', '>=', $fromDate);
        }
        if ($toDate) {
            $query->whereDate('paid_at', '<=', $toDate);
        }
        return $query->with(['user', 'paymentItems'])
            ->get()
            ->map(function ($payment) {
                $services = $payment->paymentItems->pluck('serviceTitle')->filter()->implode(', ');
                return [
                    'id' => $payment->id,
                    'user_name' => $payment->user->name ?? '-',
                    'user_email' => $payment->user->email ?? '-',
                    'gateway' => ucfirst($payment->gateway ?? 'Unknown'),
                    'services' => $services ?: '-',
                    'amount' => $payment->amount_confirmed,
                    'paid_at' => $payment->paid_at ? $payment->paid_at->format('Y-m-d H:i:s') : '-',
                ];
            })
            ->toArray();
    }

    public function getPaymentsForExport(?string $fromDate, ?string $toDate): array
    {
        // return $this->model->where('status', 'paid')
        //     ->whereDate('paid_at', '>=', $fromDate)
        //     ->whereDate('paid_at', '<=', $toDate)
        //     ->with(['user', 'paymentItems'])
        //     ->orderBy('paid_at', 'desc')
        //     ->get()
        //     ->map(function($payment) {
        //         $services = $payment->paymentItems->pluck('serviceTitle')->filter()->implode(', ');
        //         return [
        //             'id' => $payment->id,
        //             'user_name' => $payment->user->name ?? '-',
        //             'user_email' => $payment->user->email ?? '-',
        //             'user_phone' => $payment->user->phone ?? '-',
        //             'gateway' => ucfirst($payment->gateway ?? 'Unknown'),
        //             'services' => $services ?: '-',
        //             'amount_before_coupon' => $payment->amount_before_coupon,
        //             'amount_after_coupon' => $payment->amount_after_coupon,
        //             'amount_confirmed' => $payment->amount_confirmed,
        //             'status' => $payment->status,
        //             'paid_at' => $payment->paid_at ? $payment->paid_at->format('Y-m-d H:i:s') : '-',
        //             'created_at' => $payment->created_at->format('Y-m-d H:i:s'),
        //         ];
        //     })
        //     ->toArray();
        $query = $this->model->where('status', 'paid');
        if ($fromDate) {
            $query->whereDate('paid_at', '>=', $fromDate);
        }
        if ($toDate) {
            $query->whereDate('paid_at', '<=', $toDate);
        }
        return $query->with(['user', 'paymentItems'])
            ->orderBy('paid_at', 'desc')
            ->get()
            ->map(function ($payment) {
                $services = $payment->paymentItems->pluck('serviceTitle')->filter()->implode(', ');
                return [
                    'id' => $payment->id,
                    'user_name' => $payment->user->name ?? '-',
                    'user_email' => $payment->user->email ?? '-',
                    'user_phone' => $payment->user->phone ?? '-',
                    'gateway' => ucfirst($payment->gateway ?? 'Unknown'),
                    'services' => $services ?: '-',
                    'amount_before_coupon' => $payment->amount_before_coupon,
                    'amount_after_coupon' => $payment->amount_after_coupon,
                    'amount_confirmed' => $payment->amount_confirmed,
                    'status' => $payment->status,
                    'paid_at' => $payment->paid_at ? $payment->paid_at->format('Y-m-d H:i:s') : '-',
                    'created_at' => $payment->created_at->format('Y-m-d H:i:s'),
                ];
            })
            ->toArray();
    }
}
