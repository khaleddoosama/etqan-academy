<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use App\Enums\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UploadTrait;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

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
        'status',
        'response_payload',
        'paid_at',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'response_payload' => 'array',
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




    // on update
    public static function boot()
    {
        parent::boot();

        static::updated(function ($payment) {});
    }
}
