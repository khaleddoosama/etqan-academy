<?php

namespace App\Models;

use App\Traits\LogsActivityForModels;
use App\Traits\UploadTrait;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class Gallery extends Model
{
    use HasFactory, UploadTrait, LogsActivityForModels;
    protected $guarded = [];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /* methods */
    // set file Attribute
    public function setPathAttribute(UploadedFile $file)
    {
        $folderName = $this->getFolderName('galleries');

        Log::info('from Gallery folderName: ' . $folderName);

        $this->deleteIfExists($this->path); // Delete the old image if it exists
        $this->attributes['path'] = $this->uploadFile($file, $folderName, 's3');
        $this->attributes['type'] = $file->getClientOriginalExtension();
    }

    private function getFolderName($type)
    {
        $role = auth()->user()->role;
        $slug = auth()->user()->slug;

        return "users/{$role}/{$slug}/{$type}";
    }


    public function getPathUrlAttribute()
    {
        return $this->path ? Storage::disk($this->disk)->url($this->path) : null;
    }


    // on delete
    public static function boot()
    {
        parent::boot();
        static::deleting(function ($gallery) {
            $gallery->deleteIfExists($gallery->path);
        });
    }
}
