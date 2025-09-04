<?php

namespace App\Models\Traits;

use Illuminate\Support\Facades\Auth;

trait UserTrait
{
    public static function bootUserTrait()
    {
        static::creating(function ($model) {
            $model->usuario_creo = $model->usuario_creo ?? optional(Auth::user())->id;
            $model->usuario_actualizo = $model->usuario_actualizo ?? optional(Auth::user())->id;
        });

        static::updating(function ($model) {
            $model->usuario_actualizo = $model->usuario_actualizo ?? optional(Auth::user())->id;
        });
    }
}
