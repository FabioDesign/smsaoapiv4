<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'icone',
        'status',
        'target',
        'menu_id',
        'label_en',
        'label_fr',
        'position',
    ];
}
