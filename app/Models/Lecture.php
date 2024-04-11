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



    /* methods */
    // set video Attribute
    public function setVideoAttribute(UploadedFile $video)
    {
        // to lower case $this->section->course->title
        $folderName = 'courses/' . str_replace(' ', '-', strtolower($this->section->course->title)) . '/sections/' . str_replace(' ', '-', strtolower($this->section->title)). '/lectures/'. str_replace(' ', '-', strtolower($this->title)) . '/videos';

        $this->deleteIfExists($this->video); // Delete the old image if it exists

        $this->attributes['video'] = $this->uploadFile($video, $folderName);
    }
}
