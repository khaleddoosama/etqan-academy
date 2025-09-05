<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UploadTrait;
use App\Enums\PaymentStatusEnum;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class Payment extends Model
{
    use HasFactory, UploadTrait;

    protected $fillable = [
        'user_id',
        'invoice_id',
        'invoice_key',
        'gateway',
        'coupon_id',
        'discount',
        'type',
        'amount_before_coupon',
        'amount_after_coupon',
        'amount_confirmed',
        'payment_method',
        'payment_method_id',
        'status',
        'response_payload',
        'payment_data',
        'paid_at',
        'transfer_image',
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

    // get transfer_image url
    public function getTransferImageUrlAttribute()
    {
        if ($this->transfer_image) {
            return Storage::url($this->transfer_image);
        }
        return null;
    }
    // set transfer_image Attribute
    public function setTransferImageAttribute(UploadedFile $transfer_image)
    {
        $folderName = 'payments/transfer_images';
        $this->deleteIfExists($this->transfer_image); // Delete the old transfer_image if it exists
        $this->attributes['transfer_image'] = $this->uploadFile($transfer_image, $folderName, 'public');
    }

    public function scopeSearch($query, $search)
    {
        if (!empty($search)) {
            return $query->where(function ($q) use ($search) {
                $like = "%$search%";

                // Search in direct payment fields
                $q->where('invoice_id', 'like', $like)
                    ->orWhere('invoice_key', 'like', $like)
                    ->orWhere('payment_method', 'like', $like)
                    ->orWhere('gateway', 'like', $like)
                    ->orWhere('amount_after_coupon', 'like', $like)
                    ->orWhere('amount_confirmed', 'like', $like)
                    ->orWhere('status', 'like', $like);

                // Search in related user fields
                $q->orWhereHas('user', function ($userQuery) use ($like) {
                    $userQuery->where('first_name', 'like', $like)
                        ->orWhere('last_name', 'like', $like)
                        ->orWhereRaw('CONCAT(first_name, " ", last_name) LIKE ?', $like)
                        ->orWhere('email', 'like', $like)
                        ->orWhere('phone', 'like', $like);
                });

                // Search in related coupon fields
                $q->orWhereHas('coupon', function ($couponQuery) use ($like) {
                    $couponQuery->where('code', 'like', $like);
                });
            });
        }

        return $query;
    }

    // Scopes for filtering
    public function scopeFilterByUser($query, $userId)
    {
        return $query->when($userId, function ($query, $userId) {
            return $query->where('user_id', $userId);
        });
    }

    public function scopeFilterByGateway($query, $gateway)
    {
        return $query->when($gateway, function ($query, $gateway) {
            return $query->where('gateway', $gateway);
        });
    }

    public function scopeFilterByStatus($query, $status)
    {
        return $query->when($status, function ($query, $status) {
            return $query->where('status', $status);
        });
    }

    public function scopeFilterByDateRangePaidAt($query, $fromDate, $toDate)
    {
        return $query->when($fromDate, function ($query, $fromDate) {
            return $query->whereDate('paid_at', '>=', $fromDate);
        })->when($toDate, function ($query, $toDate) {
            return $query->whereDate('paid_at', '<=', $toDate);
        });
    }

        public function scopeFilterByDateRangeCreatedAt($query, $fromDate, $toDate)
    {
        return $query->when($fromDate, function ($query, $fromDate) {
            return $query->whereDate('created_at', '>=', $fromDate);
        })->when($toDate, function ($query, $toDate) {
            return $query->whereDate('created_at', '<=', $toDate);
        });
    }

    public function scopeFilterByCoupon($query, $couponId)
    {
        return $query->when($couponId, function ($query, $couponId) {
            return $query->where('coupon_id', $couponId);
        });
    }

    public function scopeWithRelations($query)
    {
        return $query->with(['user', 'coupon']);
    }

    public function scopeWithPaymentItems($query)
    {
        return $query->with(['paymentItems.course', 'paymentItems.packagePlan', 'paymentItems.courseInstallment']);
    }

    public function scopeInstapayPending($query)
    {
        return $query->where('gateway', 'instapay')->where('status', 'pending');
    }

    public function scopeInstapayPaid($query)
    {
        return $query->where('gateway', 'instapay')->where('status', 'paid');
    }

    public function scopeInstapayTotal($query)
    {
        return $query->where('gateway', 'instapay');
    }

    public function scopeFawaterakTotal($query)
    {
        return $query->where('gateway', 'fawaterak');
    }

    public function scopeFilterByAmountConfirmed($query, $amountConfirmed)
    {
        return $query->when($amountConfirmed, function ($query, $amountConfirmed) {
            return $query->where('amount_confirmed', $amountConfirmed);
        });
    }
}
