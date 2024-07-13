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
    // set Picture Attribute
    public function setPathAttribute(UploadedFile $picture)
    {

        $folderName = $this->role . '/'. $this->code .'/galleries';

        $this->deleteIfExists($this->picture); // Delete the old image if it exists
        $this->attributes['picture'] = $this->uploadImage($picture, $folderName);
    }
}
