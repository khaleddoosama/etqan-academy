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
}
