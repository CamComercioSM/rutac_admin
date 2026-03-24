<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Role extends Authenticatable
{ 
    public const ADMIN = 1;
    public const COORDINADOR = 2;
    public const ASESOR = 3;
    public const SUPERVIDOR = 4;

    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'menu_role');
    }
}
