<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\User;
use App\Enums\PaymentStatusEnum;

class PaymentStatisticsService
{
    public function getInstapayStatistics(): array
    {
        return [
            'pending' => Payment::instapayPending()->count(),
            'approved' => Payment::instapayPaid()->count(),
            'total' => Payment::instapayTotal()->count(),
            'fawaterak' => Payment::fawaterakTotal()->count(),
        ];
    }

    public function getUsersForFilter()
    {
        return User::select('id', 'first_name', 'last_name', 'email')->get();
    }

    public function getPaymentStatuses(): array
    {
        return PaymentStatusEnum::cases();
    }

    public function getGateways(): array
    {
        return [
            'fawaterak' => 'Fawaterak',
            'instapay' => 'Instapay',
        ];
    }

    public function getCouponsForFilter()
    {
        return \App\Models\Coupon::select('id', 'code')->orderBy('code')->get();
    }
}
