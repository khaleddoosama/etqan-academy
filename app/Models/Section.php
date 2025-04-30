<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use App\Traits\LogsActivityForModels;

class Section extends Model
{
    use HasFactory, Sluggable, LogsActivityForModels;

    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        // Apply global scope to order by 'position'
        static::addGlobalScope('orderByPosition', function ($query) {
            $query->orderBy('position');
        });
    }

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
        return $this->hasMany(Lecture::class)->orderBy('position');
    }

    // section
    public function parentSection()
    {
        return $this->belongsTo(Section::class, 'parent_section_id');
    }

    public function childrenSections()
    {
        return $this->hasMany(Section::class, 'parent_section_id')->with('childrenSections', 'lectures');
    }

    // get children
    public function childrens()
    {
        return $this->childrenSections()->with('lectures');
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
