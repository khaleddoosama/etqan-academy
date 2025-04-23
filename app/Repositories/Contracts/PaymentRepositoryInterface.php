<?php

namespace App\Repositories\Contracts;
use Illuminate\Database\Eloquent\Model;

interface PaymentRepositoryInterface extends BaseRepositoryInterface
{
    public function createWithItems(array $paymentData, array $paymentItemsData): Model;
}
