<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;
    // guarded
    protected $guarded = [];
    // table name
    protected $table = 'contacts';
    public function contactsable()
    {
        return $this->morphTo();
    }
}
