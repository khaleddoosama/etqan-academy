<?php

namespace App\Models;

use App\Traits\UploadTrait;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

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

    // instructor
    public function instructor()
    {
        return $this->belongsTo(Instructor::class, 'instructor_id', 'id');
    }

    // scope active
    public function scopeActive($query)
    {
        return $query->where('status', 1);
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
        // check if title == أدوبي اليستريتور
        if ($this->title == 'أدوبي اليستريتور') {
            return 22;
        } elseif ($this->title == 'أدوبي الأنديزاين') {
            return 8;
        } elseif ($this->title == 'أدوبي بريمير - قريبًا') {
            return 25;
        } elseif ($this->title == 'أدوبي أفترأفكت - قريباً') {
            return 30;
        } elseif ($this->title == 'أدوبي فوتوشوب') {
            return 20;
        } elseif ($this->title == 'الميني جرافيك') {
            return 50;
        } elseif ($this->title == 'السوبر جرافيك') {
            return 125;
        } else {
            return (int) ceil($this->sections->map(function ($section) {
                return $section->totalDuration();
            })->sum());
        }
    }

    // get students
    public function students()
    {
        return $this->belongsToMany(User::class, 'user_courses', 'course_id', 'student_id')->withPivot('status', 'id', 'created_at');
    }

    // get students count
    public function studentsCount()
    {
        // array
        $array = [
            1 => 198,
            2 => 148,
            3 => 112,
            4 => 165,
            5 => 132,
            6 => 110,
            7 => 180,
            8 => 145,
            9 => 100,
            10 => 150,
            11 => 198,
            12 => 148,
            13 => 112,
            14 => 165,
            15 => 132,
            16 => 110,
            17 => 180,
            18 => 145,
            19 => 100,
            20 => 150,
            21 => 198,
            22 => 148,
            23 => 112,
            24 => 165,
            25 => 132,
            26 => 110,
            27 => 180,
            28 => 145,
            29 => 100,
            30 => 150,
        ];

        return $this->students()->count() + $array[$this->id];
    }

    // get rating
    public function getRatingAttribute()
    {
        // random number from 4 to 5
        $array = [
            1 => 4.8,
            2 => 4.6,
            3 => 4.4,
            4 => 4.2,
            5 => 4.0,
            6 => 4.7,
            7 => 4.5,
            8 => 4.3,
            9 => 4.1,
            10 => 4.9,
            11 => 4.8,
            12 => 4.6,
            13 => 4.4,
            14 => 4.2,
            15 => 4.0,
            16 => 4.7,
            17 => 4.5,
            18 => 4.3,
            19 => 4.1,
            20 => 4.9,
            21 => 4.8,
            22 => 4.6,
            23 => 4.4,
            24 => 4.2,
            25 => 4.0,
            26 => 4.7,
            27 => 4.5,
            28 => 4.3,
            29 => 4.1,
            30 => 4.9,
        ];

        return $this->students()->avg('rating') + $array[$this->id];
    }

    // get number_of_levels attribute
    public function getNumberOfLevelsTextAttribute()
    {
        $levels = [
            1 => 'مستوي واحد',
            2 => 'مستويين',
            3 => 'ثلاث مستويات',
            4 => 'اربع مستويات',
            5 => 'خمس مستويات',
            6 => 'ست مستويات',
            7 => 'سبع مستويات',
            8 => 'ثمان مستويات',
            9 => 'تسع مستويات',
            10 => 'عشر مستويات',
        ];

        return $levels[$this->attributes['number_of_levels']] ?? 'غير محدد';
    }
    /* methods */
    // get thumbnail url
    public function getThumbnailUrlAttribute()
    {
        if ($this->thumbnail) {
            return Storage::url($this->thumbnail);
        }
        return null;
    }

    // set Image Attribute
    public function setThumbnailAttribute(UploadedFile $thumbnail)
    {
        $folderName = str_replace(' ', '-', strtolower($this->slug)) . '/thumbnails';
        $this->deleteIfExists($this->thumbnail); // Delete the old thumbnail if it exists
        $this->attributes['thumbnail'] = $this->uploadImage($thumbnail, $folderName, 960, 480, 's3');
    }
}
