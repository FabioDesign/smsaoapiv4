<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'icone',
        'status',
        'target',
        'menu_id',
        'en',
        'fr',
        'position',
    ];
    
    public $timestamps = false;
}
