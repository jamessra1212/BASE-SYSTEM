<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'img_slug',
    'fname',
    'lname',
    'minitial',
    'fullname',
    'username',
    'email',
    'email_verified_at',
    'password',
    'remember_token',
    'google_id',
    'categories',
    'roletype',
    'is_activated',
    'user_created',
    'user_updated',
    'last_login_ip'
])]
#[Hidden([
    'password',
    'remember_token',
    'google_id'
])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_activated'      => 'boolean',
        ];
    }
}
