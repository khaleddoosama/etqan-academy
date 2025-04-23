<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentGateway extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_id',
        'transaction_id',
        'amount',
        'currency',
        'status',
        'gateway',
        'customer_name',
        'customer_email',
        'customer_phone',
        'response_payload',
        'paid_at',
    ];

    protected $casts = [
        'response_payload' => 'array',
        'paid_at' => 'datetime',
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
