<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseInstallment extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'number_of_installments',
        'installment_value',
        'installment_duration',
        'description',
        'status',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
