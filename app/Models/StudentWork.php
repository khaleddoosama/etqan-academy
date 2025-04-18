<?php

namespace App\Models;

use App\Traits\UploadTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class StudentWork extends Model
{
    use HasFactory, UploadTrait;

    protected $fillable = [
        'title',
        'path',
        'type',
        'student_work_category_id',
        'position',
    ];

    public function studentWorkCategory()
    {
        return $this->belongsTo(StudentWorkCategory::class);
    }

    // set path Attribute
    public function setPathAttribute($files)
    {
        $folderName = $this->getFolderName($this->studentWorkCategory->slug);
        $file = $this->uploadAttachments([$files], $folderName)[0];

        $this->attributes['path'] = $file['path'];
        $this->attributes['type'] = $file['type'];
        $this->attributes['title'] = $file['originalName'];
    }

    public function getPathUrlAttribute()
    {
        return $this->path ? Storage::disk($this->disk)->url($this->path) : null;
    }


    private function getFolderName($type)
    {
        $model = 'student_works';
        $folder = $model . '/' . $type;
        return $folder;
    }

    public static function boot()
    {
        parent::boot();

        static::deleting(function ($studentWork) {
            $studentWork->deleteIfExists($studentWork->path);
        });

        // Apply global scope to order by 'position'
        static::addGlobalScope('orderByPosition', function ($query) {
            $query->orderBy('position');
        });
    }
}
