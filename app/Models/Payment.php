<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UploadTrait;
use App\Enums\PaymentStatusEnum;

class Payment extends Model
{
    use HasFactory, UploadTrait;

    protected $fillable = [
        'user_id',
        'invoice_id',
        'invoice_key',
        'coupon_id',
        'discount',
        'type',
        'amount_before_coupon',
        'amount_after_coupon',
        'payment_method',
        'payment_method_id',
        'status',
        'response_payload',
        'payment_data',
        'paid_at',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'response_payload' => 'array',
        'payment_data' => 'array',
        'status' => PaymentStatusEnum::class,
    ];

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function paymentItems()
    {
        return $this->hasMany(PaymentItems::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
