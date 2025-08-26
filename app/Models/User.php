<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'bp',
        'uid',
        'otp',
        'email',
        'photo',
        'otp_at',
        'gender',
        'number',
        'status',
        'user_id',
        'village',
        'comment',
        'diplome',
        'login_at',
        'password',
        'lastname',
        'firstname',
        'profile_id',
        'cellule_id',
        'birthplace',
        'profession',
        'district_id',
        'password_at',
        'birthday_at',
        'distinction',
        'number_person',
        'street_number',
        'hourse_number',
        'family_number',
        'fullname_peson',
        'nationality_id',
        'register_number',
        'residence_person',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'login_at' => 'datetime',
        'password_at' => 'datetime',
        'birthday_at' => 'date',
        'password' => 'hashed',
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
}
