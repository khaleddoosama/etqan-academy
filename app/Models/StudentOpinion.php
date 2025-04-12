<?php

namespace App\Models;

use App\Enums\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentOpinion extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'course_id',
        'opinion',
        'rate',
        'status',
    ];

    const STATUS_PENDING = 0;
    const STATUS_APPROVED = 1;
    const STATUS_REJECTED = 2;

    // قائمة بالحالات المسموح بها
    public static $statusTexts = [
        self::STATUS_PENDING => 'Pending',
        self::STATUS_APPROVED => 'Active',
        self::STATUS_REJECTED => 'Rejected',
    ];
    public static $statusColors = [
        self::STATUS_PENDING => 'warning',
        self::STATUS_APPROVED => 'success',
        self::STATUS_REJECTED => 'danger',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class)->withDefault([
            'title' => 'TheWholeSystem'
        ]);
    }

    public function scopePending($query)
    {
        return $query->where('status', 0);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 1);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 2);
    }

    // scope TheWholeSystem
    public function scopeTheWholeSystem($query)
    {
        // TheWholeSystem means course_id = null
        return $query->where('course_id', null);
    }

    public function getStatusTextAttribute()
    {
        return self::$statusTexts[$this->status];
    }
    public function getStatusColorAttribute()
    {
        return self::$statusColors[$this->status];
    }
}
