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


    protected function filterable(): array
    {
        return [
            'user_id'   => 'exact',
            'status'    => 'like',
            'created_at' => 'date',
        ];
    }
}
