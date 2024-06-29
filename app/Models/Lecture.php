<?php

namespace App\Models;

use App\Traits\UploadTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Http\UploadedFile;

class Lecture extends Model
{
    use HasFactory, Sluggable, UploadTrait;

    protected $guarded = [];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }



    public function section()
    {
        return $this->belongsTo(Section::class);
    }


    // convertedvideo
    public function convertedVideo()
    {
        return $this->hasOne(ConvertedVideo::class, 'lecture_id');
    }



    /* methods */
    // set video Attribute
    public function setVideoAttribute(UploadedFile $video)
    {
        // to lower case $this->section->course->title
        $folderName = str_replace(' ', '-', strtolower($this->section->course->title)) . '/' . str_replace(' ', '-', strtolower($this->section->title)) . '/' . str_replace(' ', '-', strtolower($this->title)) . '/videos';

        $this->deleteIfExists($this->video); // Delete the old image if it exists

        $this->attributes['video'] = $this->uploadFile($video, $folderName);
    }


    // set thumbnail Attribute
    public function setThumbnailAttribute(UploadedFile $thumbnail)
    {
        // to lower case $this->section->course->title
        $folderName = str_replace(' ', '-', strtolower($this->section->course->title)) . '/' . str_replace(' ', '-', strtolower($this->section->title)) . '/' . str_replace(' ', '-', strtolower($this->title)) . '/thumbnails';
        $this->deleteIfExists($this->thumbnail);
        $this->attributes['thumbnail'] = $this->uploadImage($thumbnail, $folderName, 960, 480);
    }

    // on change section_id
    // public function setSectionIdAttribute($value)
    // {
    //     $this->attributes['section_id'] = $value;
    //     $this->attributes['slug'] = null;
    // }
}
