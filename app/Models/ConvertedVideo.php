<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConvertedVideo extends Model
{
    use HasFactory;

    public $guarded = [];

    public function lecture()
    {
        return $this->belongsTo(Lecture::class);
    }
}
