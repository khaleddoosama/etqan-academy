<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
    use HasFactory;


    protected $guarded = [];


    // Apply a global scope to order by status desc.
    public function newQuery()
    {
        return parent::newQuery()->orderBy('status', 'desc');
    }
}
