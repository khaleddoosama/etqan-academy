<?php

namespace App\Models;

use App\Traits\UploadTrait;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;

class Course extends Model
{
    use HasFactory, UploadTrait, Sluggable;

    protected $guarded = [];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }

    protected $casts = [
        'programs' => 'array',
    ];



    // relations
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    // programs
    public function programs()
    {
        $programIds = $this->programs ?? [];
        return Program::whereIn('id', $programIds)->get();
    }

    // count number of lectures in course
    public function countLectures()
    {
        return $this->sections->map(function ($section) {
            return $section->lectures->count();
        })->sum();
    }


    // calculate total duration of lectures in course
    public function totalDuration()
    {
        return $this->sections->map(function ($section) {
            return $section->totalDuration();
        })->sum();
    }

    // get students
    public function students()
    {
        return $this->belongsToMany(User::class, 'user_courses', 'course_id', 'student_id')->withPivot('status', 'id');
    }

    /* methods */
    // set Image Attribute
    public function setThumbnailAttribute(UploadedFile $thumbnail)
    {
        $folderName = 'courses/' . str_replace(' ', '-', strtolower($this->title)) . '/thumbnails';
        $this->deleteIfExists($this->thumbnail); // Delete the old thumbnail if it exists
        $this->attributes['thumbnail'] = $this->uploadImage($thumbnail, $folderName, 960, 480);
    }
}
