<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsType extends Model
{
    public $table = 'sms_type';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'label',
    ];
    
    public $timestamps = false;
}