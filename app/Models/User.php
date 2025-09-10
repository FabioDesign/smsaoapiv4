<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
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
        'lg',
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
        'photo_at',
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
        'validator_id',
        'validator_at',
        'number_person',
        'street_number',
        'hourse_number',
        'family_number',
        'fullname_peson',
        'nationality_id',
        'register_number',
        'residence_person',
        'maritalstatus_id',
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
        'birthday_at' => 'date',
        'otp_at' => 'datetime',
        'photo_at' => 'datetime',
        'login_at' => 'datetime',
        'password_at' => 'datetime',
        'validator_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Génération de Filename unique
    public static function filenameUnique($ext)
    {
        do {
            $alfa = 'abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ0123456789';
            $string = substr(str_shuffle($alfa), 0, 15) . '.' . $ext;
        } while(self::where('photo', $string)->exists());
        return $string;
    }

    // Génération de UUID unique
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
