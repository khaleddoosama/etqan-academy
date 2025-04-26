<?php

namespace App\Models;

use App\Traits\UploadTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class PackagePlans extends Model
{
    use HasFactory, UploadTrait;

    protected $table = 'package_plans';

    protected $fillable = [
        'package_id',
        'from',
        'logo',
        'price',
        'duration',
        'device_limit',
        'description',
        'programs',
        'status',
    ];

    protected $casts = [
        'programs' => 'array',
    ];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function programs()
    {
        $programIds = $this->programs ?? [];
        return Program::whereIn('id', $programIds)->get();
    }

    // get logo url
    public function getLogoUrlAttribute()
    {
        if ($this->logo) {
            return Storage::url($this->logo);
        }
        return null;
    }

    // set Image Attribute
    public function setLogoAttribute(UploadedFile $logo)
    {
        $folderName = 'package_plans/' . str_replace(' ', '-', strtolower($this->title)) . '/logos';

        $this->deleteIfExists($this->logo);

        $this->attributes['logo'] = $this->uploadImage($logo, $folderName, 960, 480, 'public');
    }

    public static function boot()
    {
        parent::boot();
        static::deleting(function ($packagePlan) {
            dd($packagePlan);
            $packagePlan->deleteIfExists($packagePlan->logo);
        });
    }
}
