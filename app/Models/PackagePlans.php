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
        'title',
        'from',
        'price',
        'duration',
        'device_limit',
        'number_of_downloads',
        'has_ai_access',
        'has_flaticon_access',
        'description',
        'logo',
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

    // get duration text
    public function getDurationTextAttribute()
    {
        $days = $this->duration;

        if (empty($days)) {
            return null;
        }

        if ($days < 30) {
            return $days . ' يوم';
        }

        if ($days == 30) {
            return 'شهر';
        }

        if ($days == 365) {
            return 'سنة';
        }

        // Calculate months and remaining days
        $months = floor($days / 30);
        $remainingDays = $days % 30;

        $text = '';

        if ($months > 0) {
            $text .= $this->getArabicMonthText($months);
        }

        if ($remainingDays > 0) {
            if ($text) {
                $text .= ' و';
            }
            $text .= $this->getArabicDayText($remainingDays);
        }

        return $text;
    }

    private function getArabicMonthText($months)
    {
        return $months == 1 ? 'شهر' : $months . ' شهور';
    }

    private function getArabicDayText($days)
    {
        return $days == 1 ? 'يوم' : $days . ' أيام';
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
            $packagePlan->deleteIfExists($packagePlan->logo);
        });
    }
}
