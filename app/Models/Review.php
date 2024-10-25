<?php

namespace App\Models;

use App\Traits\LogsActivityForModels;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory, LogsActivityForModels;

    protected $guarded = [];

}
