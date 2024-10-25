<?php

namespace App\Models;

use App\Traits\LogsActivityForModels;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestCourse extends Model
{
    use HasFactory, LogsActivityForModels;

    protected $guarded = [];

    const STATUS_PENDING = 0;
    const STATUS_APPROVED = 1;
    const STATUS_REJECTED = 2;
    // قائمة بالحالات المسموح بها
    public static $statusTexts = [
        self::STATUS_PENDING => 'Pending',
        self::STATUS_APPROVED => 'Approved',
        self::STATUS_REJECTED => 'Rejected',
    ];
    public static $statusColors = [
        self::STATUS_PENDING => 'warning',
        self::STATUS_APPROVED => 'success',
        self::STATUS_REJECTED => 'danger',
    ];

    public function getStatusTextAttribute()
    {
        return self::$statusTexts[$this->status];
    }
    public function getStatusColorAttribute()
    {
        return self::$statusColors[$this->status];
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
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

    // get phone
    public function getPhoneAttribute($value)
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
}
