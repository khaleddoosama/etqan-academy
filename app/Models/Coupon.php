<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'discount',
        'type',
        'start_at',
        'expires_at',
        'usage_limit',
        'usage_count',
        'status',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function isValid(): bool
    {
        return $this->status &&
               ($this->starts_at === null || $this->starts_at <= now()) &&
               ($this->expires_at === null || $this->expires_at >= now()) &&
               ($this->usage_limit === null || $this->used_count < $this->usage_limit);
    }
}
