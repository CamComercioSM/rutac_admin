<?php

namespace App\Models\TablasReferencias;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class PreguntaTipo extends Model
{
    use SoftDeletes;
    protected $table = 'preguntas_tipos';

    protected $primaryKey = 'preguntatipo_id';

    protected $fillable = [
        'preguntatipo_nombre',
        'preguntatipo_opciones'
    ];

    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';
    const DELETED_AT = 'fecha_eliminacion';
}
