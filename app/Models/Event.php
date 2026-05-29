<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;
    // guarded
    protected $guarded = [];
    // table name
    protected $table = 'events';

    public function eventsable()
    {
        return $this->morphTo();
    }
}
