<?php

namespace App\Models\Soporte;

use App\Models\Traits\UserTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SoporteNovedad extends Model {
    use SoftDeletes, UserTrait;

    protected $table = 'soporte_novedades';
    protected $primaryKey = 'soporte_novedad_id';

    protected $fillable = [
        'titulo',
        'descripcion',
        'estilo_visual',
        'activo',
        'usuario_creo',
        'usuario_actualizo',
        'usuario_elimino'
    ];

    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';
    const DELETED_AT = 'fecha_eliminacion';

    // Para obtener solo lo que el usuario debe ver
    public function scopeActivas($consulta) {
        return $consulta->where('activo', true)->orderBy('fecha_creacion', 'desc');
    }
}
