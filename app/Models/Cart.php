<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function getTotalPriceAttribute()
    {
        return $this->course->discount_price * $this->quantity;
    }

    // get total price of cart for the user
    public static function getTotalPriceForUser($userId)
    {
        return Cart::where('user_id', $userId)
            ->get()
            ->sum(function ($cart) {
                return $cart->total_price;
            });
    }

    public function scopeUnique($query, $userId, $courseId)
    {
        return $query->where('user_id', $userId)->where('course_id', $courseId);
    }
}
