<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\UserTrait;
use App\Models\Traits\FechasTrait;

abstract class RutaCModel extends Model {
    // Resolvemos la colisión y configuramos el comportamiento global
    use SoftDeletes, UserTrait, FechasTrait {
        FechasTrait::getDeletedAtColumn insteadof SoftDeletes;
    }

    /**
     * Definimos la constante de eliminación lógica para todos los hijos.
     */
    const DELETED_AT = 'fecha_eliminacion';

    /**
     * Forzamos a que el modelo sepa que usamos timestamps personalizados
     * definidos en el FechasTrait.
     */
    public $timestamps = true;
}
