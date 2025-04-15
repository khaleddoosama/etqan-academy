<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseInstallment extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'course_id',
        'number_of_installments',
        'installment_amounts',
        'installment_duration',
        'description',
        'status',
    ];

    protected $casts = [
        'installment_amounts' => 'array',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
