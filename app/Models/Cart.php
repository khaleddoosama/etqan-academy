<?php

namespace App\Models;

use App\Services\StudentInstallmentService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_installment_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function courseInstallment()
    {
        return $this->belongsTo(CourseInstallment::class);
    }


    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function getTotalPriceAttribute()
    {
        $studentInstallmentService = app(StudentInstallmentService::class);

        return $studentInstallmentService->getNextInstallmentPrice($this->user_id, $this->course_installment_id);
    }

    // // get total price of cart for the user
    // public static function getTotalPriceForUser($userId)
    // {
    //     return Cart::where('user_id', $userId)
    //         ->get()
    //         ->sum(function ($cart) {
    //             return $cart->total_price;
    //         });
    // }

    public function scopeUnique($query, $userId, $courseInstallmentId)
    {
        return $query->where('user_id', $userId)->where('course_installment_id', $courseInstallmentId);
    }
}
