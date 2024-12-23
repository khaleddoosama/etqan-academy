<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseOffer extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'start_date',
        'end_date',
        'price',
        'description',
        'max_students',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
