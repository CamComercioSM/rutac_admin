<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Role extends Authenticatable
{ 
    public const ASESOR = 3;

    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'menu_role');
    }
}
