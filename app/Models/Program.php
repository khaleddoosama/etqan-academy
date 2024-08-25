<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use App\Traits\UploadTrait;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Cviebrock\EloquentSluggable\Services\SlugService;

class Program extends Model
{
    use HasFactory, Sluggable, UploadTrait;

    protected $guarded = [];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => ['name']
            ]
        ];
    }


    public function courses()
    {
        return $this->hasMany(Course::class, 'program_id');
    }


    // set Icon Attribute
    public function setIconAttribute(UploadedFile $icon)
    {
        $slug = $this->slug;
        if ($this->slug == null) {
            $slug = SlugService::createSlug(Lecture::class, 'slug', $this->name);
        }
        $folderName = 'programs/' . str_replace(' ', '-', strtolower($slug)) . '/icons';

        $this->deleteIfExists($this->icon); // Delete the old icon if it exists
        $this->attributes['icon'] = $this->uploadImage($icon, $folderName, 100, 100, 's3');
    }

    // get icon attribute
    public function getIconUrlAttribute()
    {
        if ($this->icon) {
            return Storage::url($this->icon);
        }
        return null;
    }


    // static on delete
    public static function boot()
    {
        parent::boot();
        static::deleting(function ($program) {
            if ($program->icon) {
                $program->deleteIfExists($program->icon);
            }
        });
    }
}
