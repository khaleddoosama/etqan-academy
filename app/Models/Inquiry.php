<?php

namespace App\Models;

use App\Traits\LogsActivityForModels;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
    use HasFactory, LogsActivityForModels;


    protected $guarded = [];


    // Apply a global scope to order by status desc.
    public function newQuery()
    {
        return parent::newQuery()->orderBy('status', 'asc')->orderBy('created_at', 'desc');
    }

    // get phone
    public function getPhoneAttribute($value)
    {
        // if not have country code +20 add it
        if (substr($value, 0, 3) != '+20') {
            if (substr($value, 0, 1) == '0') {
                return '+2' . $value;
            } else {
                return '+20' . $value;
            }
        } else {
            return $value;
        }
    }
}
