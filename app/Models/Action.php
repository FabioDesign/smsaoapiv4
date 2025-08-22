<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Action extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'icone',
        'status',
        'label_fr',
        'label_en',
        'position',
    ];
}
