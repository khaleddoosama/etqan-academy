<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Traits\UploadTrait;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Http\UploadedFile;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, UploadTrait, Sluggable;


    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];


    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];


    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => ['first_name', 'last_name']
            ]
        ];
    }

    // get name
    public function getNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    // User Active Now
    public function UserOnline()
    {
        return Cache::has('user-is-online' . $this->id);
    }

    // scope student
    public function scopeStudent($query)
    {
        return $query->where('role', 'student');
    }

    // scope instructor
    public function scopeInstructor($query)
    {
        return $query->where('role', 'instructor');
    }

    // scope admin
    public function scopeAdmin($query)
    {
        return $query->where('role', 'admin');
    }

    // scope student and pending
    public function scopeStudentPending($query)
    {
        return $query->where('role', 'student')->where('status', 0);
    }

    // scope student and active
    public function scopeStudentActive($query)
    {
        return $query->where('role', 'student')->where('status', 1);
    }

    // scope student and inactive
    public function scopeStudentInactive($query)
    {
        return $query->where('role', 'student')->where('status', 2)->orWhere('status', 3);
    }

    // relations
    // gallery
    public function galleries()
    {
        return $this->hasMany(Gallery::class);
    }

    // courses
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'user_courses', 'student_id', 'course_id')->withPivot('id', 'completed', 'rating', 'review', 'progress', 'status', 'created_at');
    }

    // referrals
    public function referralsParent()
    {
        return $this->hasMany(Referral::class, 'parent_user', 'id');
    }

    /* methods */
    // set Picture Attribute
    public function setPictureAttribute(UploadedFile $picture)
    {

        $folderName = $this->role . '/' . $this->code . '/pictures';

        $this->deleteIfExists($this->picture); // Delete the old image if it exists
        $this->attributes['picture'] = $this->uploadImage($picture, $folderName);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            $user->code = strtoupper(Str::random(15));
        });

        // static::created(function ($user) {

        // });
    }
}
