<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\PaymentType;

class PaymentItems extends Model
{
    use HasFactory;

    protected $table = 'payment_items';

    protected $fillable = [
        'course_installment_id',
        'course_id',
        'package_plan_id',
        'payment_id',
        'payment_type',
        'amount',
    ];
    protected $casts = [
        'payment_type' => PaymentType::class,
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function courseInstallment()
    {
        return $this->belongsTo(CourseInstallment::class);
    }

    public function packagePlan()
    {
        return $this->belongsTo(PackagePlans::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    //get serviceTitle
    public function getServiceTitleAttribute()
    {
        if ($this->course) {
            return $this->course->title;
        } else {
            return $this->packagePlan->title;
        }
    }
}
