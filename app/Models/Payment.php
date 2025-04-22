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
        'payment_method',
        'transfer_identifier',
        'transfer_image',
        'whatsapp_number',
        'amount_before_coupon',
        'amount_after_coupon',
        'amount_confirmed',
        'coupon_id',
        'discount',
        'type',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
    ];

    protected $casts = [
        'payment_method' => PaymentMethod::class,
        'status' => Status::class,
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
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

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function setTransferImageAttribute(UploadedFile $transferImage)
    {
        $folderName = 'payments';

        $this->deleteIfExists($this->transfer_image); // Delete the old image if it exists
        $this->attributes['transfer_image'] = $this->uploadImage($transferImage, $folderName, 640, 480, 'public');
    }

    public function getTransferImageUrlAttribute($value)
    {
        if ($this->transfer_image) {
            return Storage::url($this->transfer_image);
        }
        return null;
    }

    // get whatsapp number
    public function getWhatsappNumberAttribute($value)
    {
        // if not have country code +20 add it
        if (substr($value, 0, 3) != '+20') {
            if (substr($value, 0, 1) == '0') {
                return '+2' . $value;
            } else {
                return '+20' . $value;
            }
        } else {
            return $value;
        }
    }

    // on update
    public static function boot()
    {
        parent::boot();

        static::updated(function ($payment) {
            if ($payment->status == Status::APPROVED) {
                $payment->approved_at = now();
                $payment->approved_by = auth()->user()->id;
                $payment->save();
            } elseif ($payment->status == Status::REJECTED) {
                $payment->rejected_at = now();
                $payment->rejected_by = auth()->user()->id;
                $payment->save();
            }
        });
    }
}
