<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeParent extends Model
{
    public $table = 'type_parent';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'en',
        'fr',
    ];
    
    public $timestamps = false;
}