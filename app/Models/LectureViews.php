<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LectureViews extends Model
{
    use HasFactory;


    protected $guarded = [];

    public function lecture()
    {
        return $this->belongsTo(Lecture::class);
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // gel lecture views count
    public function getLectureViewsCountAttribute()
    {
        return LectureViews::where('lecture_id', $this->lecture_id)->count();
    }
}
