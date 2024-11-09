<?php

namespace App\Models;

use App\Traits\LogsActivityForModels;
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
    use HasFactory, Sluggable, UploadTrait, LogsActivityForModels;

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

    // get duration (sum hours and minutes and seconds)
    public function duration()
    {
        return $this->hours + ($this->minutes / 60) + ($this->seconds / 3600);
    }

    // convertedvideo
    public function convertedVideo()
    {
        return $this->hasOne(ConvertedVideo::class, 'lecture_id');
    }

    // get video url
    public function getVideoUrlAttribute()
    {
        if ($this->video != null) {
            return "https://www.youtube.com/embed/" . $this->video . "?rel=0";
        } else {
            return null;
        }
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
