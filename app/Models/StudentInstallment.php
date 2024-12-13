<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentInstallment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'course_installment_id',
        'amount',
        'remaining_amount',
        'due_date',
    ];

    public function courseInstallment()
    {
        return $this->belongsTo(CourseInstallment::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class);
    }
}
