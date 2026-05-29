<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;
    // guarded
    protected $guarded = [];
    public function imagesable()
    {
        return $this->morphTo();
    }
    // table name
    protected $table = 'images';
}
