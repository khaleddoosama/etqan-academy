<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use App\Enums\PaymentType;
use App\Enums\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UploadTrait;
use Illuminate\Http\UploadedFile;

class PaymentDetails extends Model
{
    use HasFactory, UploadTrait;

    protected $fillable = [
        'course_id',
        'user_id',
        'whatsapp_number',
        'payment_type',
        'payment_method',
        'transfer_number',
        'transfer_image',
        'status',
    ];

    protected $casts = [
        'payment_type' => PaymentType::class,
        'payment_method' => PaymentMethod::class,
        'status' => Status::class,
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
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

    // Apply a global scope to order by status desc.
    public function newQuery()
    {
        return parent::newQuery()->orderBy('status', 'asc')->orderBy('created_at', 'desc');
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

    /* methods */
    public function setTransferImageAttribute(UploadedFile $transferImage)
    {
        $folderName = 'payments';

        $this->deleteIfExists($this->transfer_image); // Delete the old image if it exists
        $this->attributes['transfer_image'] = $this->uploadImage($transferImage, $folderName, 640, 480, 'public');
    }
}
