<?php

namespace App\Models\Traits;

use Illuminate\Support\Facades\Auth;

trait UserTrait
{
    public static function bootUserTrait()
    {
        static::creating(function ($model) {
            $model->usuario_creo = $model->usuario_creo ?? optional(Auth::user())->id;
            $model->usuario_actualizo = optional(Auth::user())->id;
        });

        static::updating(function ($model) {
            $model->usuario_actualizo = optional(Auth::user())->id;
        });

        static::deleting(function ($model) {
            if (in_array(\Illuminate\Database\Eloquent\SoftDeletes::class, class_uses_recursive($model))) {
                $model->usuario_elimino = optional(Auth::user())->id;
                $model->saveQuietly();
            } else {
                $model->usuario_elimino = optional(Auth::user())->id;
            }
        });
    }
}
