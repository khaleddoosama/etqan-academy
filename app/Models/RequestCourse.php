<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestCourse extends Model
{
    use HasFactory;

    protected $guarded = [];

    const STATUS_PENDING = 0;
    const STATUS_REPLIED = 1;
    // قائمة بالحالات المسموح بها
    public static $statusTexts = [
        self::STATUS_PENDING => 'Pending',
        self::STATUS_REPLIED => 'Replied',
    ];
    public static $statusColors = [
        self::STATUS_PENDING => 'warning',
        self::STATUS_REPLIED => 'success',
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

    // Apply a global scope to order by status desc.
    public function newQuery()
    {
        return parent::newQuery()->orderBy('status', 'asc')->orderBy('created_at', 'desc');
    }
}
