<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Phone extends Model
{
    use HasFactory;
    // guarded
    protected $guarded = [];
    // table name
    protected $table = 'phones';
    public function phonesable()
    {
        return $this->morphTo();
    }
}
