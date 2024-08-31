<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WithdrawalRequest extends Model
{
    use HasFactory;

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

    protected $guarded = [];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusTextAttribute()
    {
        return self::$statusTexts[$this->status];
    }
    public function getStatusColorAttribute()
    {
        return self::$statusColors[$this->status];
    }

    // Apply a global scope to order by status desc.
    public function newQuery()
    {
        return parent::newQuery()->orderBy('status', 'asc')->orderBy('created_at', 'desc');
    }
}
