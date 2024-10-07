<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class Section extends Model
{
    use HasFactory, Sluggable;

    protected $guarded = [];


    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }


    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function lectures()
    {
        return $this->hasMany(Lecture::class)->where('processed', 1)->orderBy('position');
    }

    // calculate total duration of lectures in section
    public function totalDuration()
    {
        // sum hours and minutes and seconds
        return $this->lectures->sum(function ($lecture) {
            return $lecture->duration();
        });
    }
}
