<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Spouse extends Model
{
    use HasFactory;

    protected $fillable = [
        'rank',
        'status',
        'user_id',
        'filename',
        'spouse_id',
        'wedding_at',
        'requestdoc_id',
    ];
}
