<?php

namespace App\Models\Traits;

use Illuminate\Support\Facades\Auth;

trait UserTrait
{
    public static function bootUserTrait()
    {
        static::creating(function ($model) {
            $model->created_by = $model->created_by ?? optional(Auth::user())->name;
            $model->updated_by = $model->updated_by ?? optional(Auth::user())->name;
        });

        static::updating(function ($model) {
            $model->updated_by = $model->updated_by ?? optional(Auth::user())->name;
        });
    }
}
