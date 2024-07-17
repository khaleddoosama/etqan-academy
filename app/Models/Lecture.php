<?php

namespace App\Models;

use App\Traits\UploadTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

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


    public function course()
    {
        // lecture->section->course
        return $this->section->course();
    }





    // convertedvideo
    public function convertedVideo()
    {
        return $this->hasOne(ConvertedVideo::class, 'lecture_id');
    }

    // get video
    public function getConvertedVideosAttribute()
    {
        if ($this->convertedVideo) {
            $qualities = [1080, 720, 480, 360, 240];
            $i = array_search($this->quality, $qualities); // 360
            $videos = [];

            // get all videos from quality or less
            foreach (array_slice($qualities, $i) as $quality) {
                $videos[] = Storage::url($this->convertedVideo->{"mp4_Format_$quality"});
                $videos[] = Storage::url($this->convertedVideo->{"webm_Format_$quality"});
            }

            return $videos;
        }
    }



    /* methods */
    // set video Attribute
    public function setVideoAttribute(UploadedFile $video)
    {
        // to lower case $this->section->course->title
        $folderName = str_replace(' ', '-', strtolower($this->section->course->slug)) . '/' . str_replace(' ', '-', strtolower($this->section->slug)) . '/' . str_replace(' ', '-', strtolower($this->slug)) . '/videos';

        $this->deleteIfExists($this->video); // Delete the old video if it exists

        $this->deleteIfExists($this->convertedVideo?->mp4_Format_240); // Delete the old video if it
        $this->deleteIfExists($this->convertedVideo?->mp4_Format_360); // Delete the old video if it
        $this->deleteIfExists($this->convertedVideo?->mp4_Format_480); // Delete the old video if it
        $this->deleteIfExists($this->convertedVideo?->mp4_Format_720); // Delete the old video if it
        $this->deleteIfExists($this->convertedVideo?->mp4_Format_1080); // Delete the old video if it

        $this->deleteIfExists($this->convertedVideo?->webm_Format_240); // Delete the old video if it
        $this->deleteIfExists($this->convertedVideo?->webm_Format_360); // Delete the old video if it
        $this->deleteIfExists($this->convertedVideo?->webm_Format_480); // Delete the old video if it
        $this->deleteIfExists($this->convertedVideo?->webm_Format_720); // Delete the old video if it
        $this->deleteIfExists($this->convertedVideo?->webm_Format_1080); // Delete the old video if it

        $this->attributes['video'] = $this->uploadFile($video, $folderName);
    }


    // set thumbnail Attribute
    public function setThumbnailAttribute(UploadedFile $thumbnail)
    {
        // to lower case $this->section->course->title
        $folderName = str_replace(' ', '-', strtolower($this->section->course->slug)) . '/' . str_replace(' ', '-', strtolower($this->section->slug)) . '/' . str_replace(' ', '-', strtolower($this->slug)) . '/thumbnails';
        $this->deleteIfExists($this->thumbnail);
        $this->attributes['thumbnail'] = $this->uploadImage($thumbnail, $folderName, 960, 480, 's3');
    }

    // get thumbnail ur
    public function getThumbnailUrlAttribute()
    {
        return Storage::url($this->thumbnail);
    }
    // on change section_id
    // public function setSectionIdAttribute($value)
    // {
    //     $this->attributes['section_id'] = $value;
    //     $this->attributes['slug'] = null;
    // }
}
