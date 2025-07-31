<?php

namespace App\Models\TablasReferencias;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class PreguntaGrupo extends Model
{
    use SoftDeletes;
    protected $table = 'preguntas_grupos';

    protected $primaryKey = 'preguntagrupo_id';

    protected $fillable = [
        'preguntagrupo_nombre'
    ];

    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';
    const DELETED_AT = 'fecha_eliminacion';
}
