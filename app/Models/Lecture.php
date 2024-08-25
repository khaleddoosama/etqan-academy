<?php

namespace App\Models;

use App\Traits\UploadTrait;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
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

    // cast
    protected $casts = [
        'attachments' => 'array'
    ];


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

    // get best quality video
    public function getBestQualityVideoAttribute()
    {
        if ($this->convertedVideo) {
            $qualities = [1080, 720, 480, 360, 240];
            $i = array_search($this->quality, $qualities); // 360

            // get all videos from quality or less
            foreach (array_slice($qualities, $i) as $quality) {
                if ($this->convertedVideo->{"mp4_Format_$quality"} != null) {
                    return Storage::url($this->convertedVideo->{"mp4_Format_$quality"});
                }
            }
        }
    }

    // get thumbnail ur
    public function getThumbnailUrlAttribute()
    {
        if ($this->thumbnail != null) {
            return Storage::url($this->thumbnail);
        } else {
            return null;
        }
    }

    /* methods */
    // set video Attribute
    public function setVideoAttribute($path)
    {
        // to lower case $this->section->course->title
        // $folderName = str_replace(' ', '-', strtolower($this->section->course->slug)) . '/' . str_replace(' ', '-', strtolower($this->section->slug)) . '/' . str_replace(' ', '-', strtolower($this->slug)) . '/videos';

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

        // $this->attributes['video'] = $this->uploadFile($video, $folderName);
        $this->attributes['video'] = $path;
    }


    // set thumbnail Attribute
    public function setThumbnailAttribute(UploadedFile $thumbnail)
    {
        $folderName = $this->getFolderName('thumbnails');


        $this->deleteIfExists($this->thumbnail);
        $this->attributes['thumbnail'] = $this->uploadImage($thumbnail, $folderName, 960, 480, 's3');
    }



    // set attachments Attribute
    public function setAttachmentsAttribute($attachments)
    {
        if (is_array($attachments)) {

            $folderName = $this->getFolderName('attachments');

            $newAttachments = $this->uploadAttachments($attachments, $folderName);
            $oldAttachments = $this->attributes['attachments'] ?? [];

            // Ensure oldAttachments is an array
            if (!is_array($oldAttachments)) {
                $oldAttachments = json_decode($oldAttachments, true);
            }

            $combined = array_merge($newAttachments, $oldAttachments);
        $this->attributes['attachments'] = json_encode($combined);  // Encode as JSON before saving
        }
    }

    private function getFolderName($type)
    {
        $slug = $this->slug;
        if ($this->slug == null) {
            $slug = SlugService::createSlug(Lecture::class, 'slug', $this->title);
        }

        return str_replace(' ', '-', strtolower($this->section->course->slug)) . '/' . str_replace(' ', '-', strtolower($this->section->slug)) . '/' . str_replace(' ', '-', strtolower($slug)) . '/' . $type;
    }

    // get attachments
    public function getAttachmentsUrlAttribute()
    {
        $attachments = json_decode($this->attributes['attachments'], true);
        $attachments = array_map(function ($attachment) {
            return [
                'name' => $attachment['originalName'],
                'url' => Storage::url($attachment['path']),
                'type' => $attachment['type'],

            ];
        }, $attachments ?? []);

        return $attachments;
    }


    // on change section_id
    // public function setSectionIdAttribute($value)
    // {
    //     $this->attributes['section_id'] = $value;
    //     $this->attributes['slug'] = null;
    // }
}
