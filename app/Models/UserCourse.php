<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCourse extends Model
{
    use HasFactory;


    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;
    // قائمة بالحالات المسموح بها
    public static $statusTexts = [
        self::STATUS_ACTIVE => 'Active',
        self::STATUS_INACTIVE => 'Inactive',
    ];
    public static $statusColors = [
        self::STATUS_ACTIVE => 'success',
        self::STATUS_INACTIVE => 'danger',
    ];
    protected $guarded = ['id'];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
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
