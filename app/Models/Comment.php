<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;
    // guarded
    protected $guarded = [];
    public function commentsable()
    {
        return $this->morphTo();
    }
    // table name
    protected $table = 'comments';

    public function replies()
    {
        return $this->morphMany(Reply::class, 'repliesable');
    }
}
