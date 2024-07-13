<?php

namespace App\Models;

use App\Traits\UploadTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;

class Gallery extends Model
{
    use HasFactory, UploadTrait;
    protected $guarded = [];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /* methods */
    // set file Attribute
    public function setPathAttribute(UploadedFile $file)
    {

        $folderName = $this->role . '/'. $this->code .'/galleries';

        $this->deleteIfExists($this->path); // Delete the old image if it exists
        $this->attributes['path'] = $this->uploadFile($file, $folderName);
    }
}
