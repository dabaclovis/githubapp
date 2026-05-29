<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    use HasFactory;
    // guarded
    protected $guarded = [];
    // table name
    protected $table = 'replies';

    public function repliesable()
    {
        return $this->morphTo();
    }
}
