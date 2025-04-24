<?php

namespace App\Repositories\Eloquent;

use App\Models\Payment;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class PaymentRepository extends BaseRepository implements PaymentRepositoryInterface
{
    protected function model(): Payment
    {
        return new Payment();
    }

    public function createWithItems(array $paymentData, array $paymentItemsData): Model
    {
        return DB::transaction(function () use ($paymentData, $paymentItemsData) {
            $payment = $this->model->create($paymentData);

            $payment->paymentItems()->createMany($paymentItemsData);

            return $payment->load('paymentItems');
        });
    }

    protected function filterable(): array
    {
        return [
            'user_id'   => 'exact',
            'status'    => 'like',
            'created_at' => 'date',
        ];
    }
}
