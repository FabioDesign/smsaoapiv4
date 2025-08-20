<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

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
        'email',
        'photo',
        'gender',
        'number',
        'status',
        'user_id',
        'village',
        'diplome',
        'login_at',
        'password',
        'lastname',
        'firstname',
        'cellule_id',
        'birthplace',
        'profession',
        'district_id',
        'password_at',
        'birthday_at',
        'distinction',
        'street_number',
        'hourse_number',
        'family_number',
        'fullname_peson',
        'contact_person',
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
        'birthday_at' => 'datetime',
        'password' => 'hashed',
    ];
}
