<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    // guarded
    protected $guarded = [];
    // table name
    protected $table = 'posts';
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentsable');
    }

    public function imagesable()
    {
        return $this->morphOne(Image::class, 'imagesable');
    }
}
