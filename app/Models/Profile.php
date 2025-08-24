<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'uid',
        'status',
        'label_en',
        'label_fr',
        'deleted_at',
        'created_user',
        'updated_user',
        'deleted_user',
        'description_en',
        'description_fr',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'deleted_at' => 'datetime',
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

    public function permissions()
    {
        return $this->hasMany(Permission::class);
    }

    public function users()
    {
        return $this->hasMany(User::class, 'profile_id');
    }
}
