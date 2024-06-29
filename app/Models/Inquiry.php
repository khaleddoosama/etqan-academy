<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
    use HasFactory;


    protected $guarded = [];


    // Apply a global scope to order by created_at desc.
    public function newQuery()
    {
        return parent::newQuery()->orderBy('status', 'desc');
    }
}
