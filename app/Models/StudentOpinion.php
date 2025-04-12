<?php

namespace App\Models;

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

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
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
}
