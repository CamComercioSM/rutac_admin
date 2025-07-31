<?php

namespace App\Models\Programas;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProgramaIndicador extends Model
{
    use SoftDeletes;

    protected $table = 'programa_indicadores';
    protected $primaryKey = 'indicador_id';

    protected $fillable = [
        'indicador_nombre'
    ];

    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';
    const DELETED_AT = 'fecha_eliminacion';
}
