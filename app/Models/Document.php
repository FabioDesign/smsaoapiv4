<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'en',
        'fr',
        'uid',
        'code',
        'number',
        'status',
        'amount',
        'user_id',
        'period_id',
        'created_user',
        'updated_user',
        'description_en',
        'description_fr',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uid)) {
                $model->uid = Str::uuid()->toString();
            }
        });
    }

    public function files()
    {
        return $this->hasMany(File::class, 'document_id');
    }
}
