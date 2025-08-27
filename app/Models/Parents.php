<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Parents extends Model
{
    public $table = 'parents';

    protected $fillable = [
        'type_id',
        'user_id',
        'lastname',
        'firstname',
        'parent_id',
    ];
    
    public $timestamps = false;
}
