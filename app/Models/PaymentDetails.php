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

    /* methods */
    public function setTransferImageAttribute(UploadedFile $transferImage)
    {
        $folderName = 'payments';

        $this->deleteIfExists($this->transfer_image); // Delete the old image if it exists
        $this->attributes['transfer_image'] = $this->uploadImage($transferImage, $folderName, 640, 480, 'public');
    }
}
