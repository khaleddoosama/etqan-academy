<?php

namespace App\Models;

use App\Traits\LogsActivityForModels;
use App\Traits\UploadTrait;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class Course extends Model
{
    use HasFactory, UploadTrait, Sluggable, LogsActivityForModels;

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

    public function programs()
    {
        $programIds = $this->programs ?? [];
        return Program::whereIn('id', $programIds)->get();
    }

    public function instructor()
    {
        return $this->belongsTo(Instructor::class, 'instructor_id', 'id');
    }

    public function offer()
    {
        return $this->hasOne(CourseOffer::class)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now());
    }

    public function courseInstallments()
    {
        return $this->hasMany(CourseInstallment::class);
    }

    public function userCourses()
    {
        return $this->hasMany(UserCourse::class);
    }

    // progress
    public function getProgress($user_id)
    {
        return $this->userCourses()->where('student_id', $user_id)->first()->progress ?? 0;
    }

    // StudentOpinion
    public function studentOpinions()
    {
        return $this->hasMany(StudentOpinion::class)->where('status', 1);
    }
    // scopes
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function countLectures()
    {
        return $this->sections->map(function ($section) {
            return $section->lectures->count();
        })->sum();
    }


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

    public function students()
    {
        return $this->belongsToMany(User::class, 'user_courses', 'course_id', 'student_id')->withPivot('status', 'id', 'created_at');
    }

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

    public function getTotalPriceAttribute()
    {
        if ($this->offer) {
            return $this->offer->price;
        } elseif ($this->discount_price) {
            return $this->discount_price;
        } else {
            return $this->price;
        }
    }

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
        $this->attributes['thumbnail'] = $this->uploadImage($thumbnail, $folderName, 960, 480, 'public');
    }

    // get diploma_details_file url
    public function getDiplomaDetailsFileUrlAttribute()
    {
        if ($this->diploma_details_file) {
            return Storage::url($this->diploma_details_file);
        }
        return null;
    }
    // set diploma_details_file Attribute
    public function setDiplomaDetailsFileAttribute(UploadedFile $diploma_details_file)
    {
        $folderName = str_replace(' ', '-', strtolower($this->slug)) . '/diploma_details_files';
        $this->deleteIfExists($this->diploma_details_file); // Delete the old diploma_details_file if it exists
        $this->attributes['diploma_details_file'] = $this->uploadFile($diploma_details_file, $folderName, 'public');
    }

    // when created
    public static function boot()
    {
        parent::boot();

        static::created(function ($course) {
            $course->courseInstallments()->createMany([
                ['name' => 'cash', 'installment_amounts' => [$course->discount_price]],
            ]);
        });
    }
}
