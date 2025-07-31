<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Menu extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $table = 'menu';

    protected $fillable = [
        'label',
        'icon',
        'url'
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'menu_role');
    }
}
