<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use App\Traits\UploadTrait;
use Cviebrock\EloquentSluggable\Sluggable;

class Package extends Model
{
    use HasFactory, UploadTrait, Sluggable;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'meaning_description',
        'features',
        'logo',
        'programs',
    ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => ['title']
            ]
        ];
    }

    protected $casts = [
        'programs' => 'array',
        'features' => 'array',
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

    // get logo url
    public function getLogoUrlAttribute()
    {
        if ($this->logo) {
            return Storage::url($this->logo);
        }
        return null;
    }

    // set Image Attribute
    public function setLogoAttribute(UploadedFile $logo)
    {
        $folderName = 'packages/' . str_replace(' ', '-', strtolower($this->title)) . '/logos';

        $this->deleteIfExists($this->logo);

        $this->attributes['logo'] = $this->uploadImage($logo, $folderName, 960, 480, 'public');
    }

    public static function boot()
    {
        parent::boot();
        static::deleting(function ($package) {
            $package->deleteIfExists($package->logo);
        });
    }
}
