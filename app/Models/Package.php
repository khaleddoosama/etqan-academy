<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'programs',
    ];

    protected $casts = [
        'programs' => 'array',
    ];

    public function programs()
    {
        $programIds = $this->programs ?? [];
        return Program::whereIn('id', $programIds)->get();
    }

    public function packagePlans()
    {
        return $this->hasMany(PackagePlans::class);
    }
}
