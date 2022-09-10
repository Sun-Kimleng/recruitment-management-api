<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use App\Notifications\ResetPasswordNotification;

class User extends Authenticatable implements MustVerifyEmail
{   
    protected $table='users';

    use HasApiTokens, HasFactory, Notifiable;



    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'username',
        'fb_id',
        'fb_token',
        'user_status',
        'email',
        'avatar',
        'password',
        'role',
    ];
    

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function jobs(){
        return $this->hasMany(Job::class, 'added_by', 'id');
    }

    public function candidates(){
        return $this->hasOne(Candidate::class);
    }
    public function sendPasswordResetNotification($token)
    {

        $url = 'http://localhost:3000/admin/reset_password?token=' . $token;

        $this->notify(new ResetPasswordNotification($url));
    }
}
