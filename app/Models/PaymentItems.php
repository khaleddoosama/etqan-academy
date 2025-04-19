<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentItems extends Model
{
    use HasFactory;

    protected $table = 'payment_items';

    protected $fillable = [
        'course_installment_id',
        'payment_id',
        'amount',
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function courseInstallment()
    {
        return $this->belongsTo(CourseInstallment::class);
    }
}
