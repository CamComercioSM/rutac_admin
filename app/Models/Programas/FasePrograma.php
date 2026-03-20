<?php

namespace App\Models\Programas;

use App\Models\Traits\UserTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FasePrograma extends Model {
    use SoftDeletes, UserTrait;

    protected $table = 'fases_programas';
    protected $primaryKey = 'fase_id';

    protected $fillable = [
        'fase_nombre',
        'fase_descripcion',
        'fase_orden',
        'fase_activa',
    ];

    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';
    const DELETED_AT = 'fecha_eliminacion';
}
