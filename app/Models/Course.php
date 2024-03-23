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


    // relations
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function sections()
    {
        return $this->hasMany(Section::class);
    }


    /* methods */
    // set Image Attribute
    public function setThumbnailAttribute(UploadedFile $thumbnail)
    {
        $folderName = 'course/thumbnails';
        $this->deleteIfExists($this->thumbnail); // Delete the old thumbnail if it exists
        $this->attributes['thumbnail'] = $this->uploadImage($thumbnail, $folderName, 960, 480);
    }
}
